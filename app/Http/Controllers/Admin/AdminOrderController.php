<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartnerProfile;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Models\ShipmentTrackingEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:pending,processing,dispatched,delivered,cancelled'],
        ]);

        $query = Order::query()
            ->with(['customerProfile.user', 'items'])
            ->withCount('items');

        if (! empty($validated['status'])) {
            $query->where(function ($statusQuery) use ($validated) {
                $statusQuery->where('fulfillment_status', $validated['status'])
                    ->orWhere(function ($fallbackQuery) use ($validated) {
                        $fallbackQuery->whereNull('fulfillment_status')
                            ->where('status', $validated['status']);
                    });
            });
        }

        if (! empty($validated['search'])) {
            $term = trim($validated['search']);
            $query->where(function ($builder) use ($term) {
                $builder->where('order_id', 'like', '%' . $term . '%')
                    ->orWhere('reference_id', 'like', '%' . $term . '%')
                    ->orWhereHas('customerProfile.user', function ($userQuery) use ($term) {
                        $userQuery->where('name', 'like', '%' . $term . '%')
                            ->orWhere('email', 'like', '%' . $term . '%')
                            ->orWhere('mobile', 'like', '%' . $term . '%');
                    });
            });
        }

        $orders = $query->latest('placed_at')->latest('id')->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customerProfile.user',
            'items.product',
            'addresses',
            'statusHistories.changedBy',
            'shipments.deliveryPartnerAssignments.deliveryPartnerProfile',
        ]);

        $deliveryPartners = DeliveryPartnerProfile::query()
            ->where('status', 'active')
            ->where('verification_status', 'verified')
            ->orderBy('display_name')
            ->get();

        $shippingAddress = $order->addresses->firstWhere('address_type', 'shipping');

        return view('admin.orders.show', compact('order', 'deliveryPartners', 'shippingAddress'));
    }

    public function dispatch(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_partner_id' => [
                'required',
                'integer',
                'exists:delivery_partner_profiles,id',
            ],
            'tracking_number' => ['required', 'string', 'max:191'],
            'shipping_notes' => ['nullable', 'string', 'max:1000'],
            'carrier_name' => ['nullable', 'string', 'max:191'],
        ]);

        DB::transaction(function () use ($order, $validated, $request) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (in_array($lockedOrder->fulfillment_status, ['delivered', 'cancelled'], true)) {
                throw ValidationException::withMessages([
                    'order' => 'A delivered or cancelled order cannot be dispatched.',
                ]);
            }

            $partner = DeliveryPartnerProfile::query()
                ->whereKey($validated['delivery_partner_id'])
                ->where('status', 'active')
                ->where('verification_status', 'verified')
                ->firstOrFail();

            $address = $lockedOrder->addresses()->where('address_type', 'shipping')->first();
            if (! $address) {
                throw ValidationException::withMessages([
                    'order' => 'The order has no saved shipping address. Dispatch cannot proceed.',
                ]);
            }

            $shipment = Shipment::query()
                ->where('order_id', $lockedOrder->id)
                ->whereNotIn('status', ['cancelled', 'returned', 'failed'])
                ->latest('id')
                ->first();

            if (! $shipment) {
                $shipment = Shipment::create([
                    'order_id' => $lockedOrder->id,
                    'shipment_id' => 'SHP-' . strtoupper(Str::random(16)),
                    'status' => 'shipped',
                    'carrier_name' => $validated['carrier_name'] ?: $partner->display_name,
                    'tracking_number' => $validated['tracking_number'],
                    'shipping_method' => 'standard',
                    'recipient_name' => $address->name,
                    'recipient_phone' => $address->phone,
                    'address_line_1' => $address->address_line_1,
                    'address_line_2' => $address->address_line_2,
                    'landmark' => $address->landmark,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country,
                    'postal_code' => $address->postal_code,
                    'shipping_charge' => $lockedOrder->shipping_amount,
                    'shipped_at' => now(),
                    'delivery_notes' => $validated['shipping_notes'] ?? null,
                ]);
            } else {
                $shipment->update([
                    'status' => 'shipped',
                    'carrier_name' => $validated['carrier_name'] ?: $partner->display_name,
                    'tracking_number' => $validated['tracking_number'],
                    'shipped_at' => now(),
                    'delivery_notes' => $validated['shipping_notes'] ?? $shipment->delivery_notes,
                ]);
            }

            $shipment->deliveryPartnerAssignments()->updateOrCreate(
                ['delivery_partner_profile_id' => $partner->id],
                [
                    'status' => 'assigned',
                    'assigned_at' => now(),
                    'remarks' => $validated['shipping_notes'] ?? null,
                ]
            );

            ShipmentTrackingEvent::create([
                'shipment_id' => $shipment->id,
                'event_code' => 'DISPATCHED',
                'event_status' => 'shipped',
                'description' => 'Order dispatched by admin.',
                'event_at' => now(),
                'source' => 'admin',
                'metadata' => ['tracking_number' => $validated['tracking_number']],
            ]);

            $oldStatus = $lockedOrder->fulfillment_status;
            $lockedOrder->update([
                'status' => 'dispatched',
                'fulfillment_status' => 'dispatched',
                'dispatched_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'changed_by_user_id' => $request->user()?->id,
                'from_status' => $oldStatus,
                'to_status' => 'dispatched',
                'source' => 'admin',
                'remarks' => 'Dispatched via ' . $partner->display_name . '. Tracking: ' . $validated['tracking_number']
                    . (filled($validated['shipping_notes'] ?? null) ? ' — ' . $validated['shipping_notes'] : ''),
                'changed_at' => now(),
            ]);
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order dispatch details saved successfully.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,dispatched,delivered,cancelled'],
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $validated, $request) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            $oldStatus = $lockedOrder->fulfillment_status ?: $lockedOrder->status;
            $newStatus = $validated['status'];

            if ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'A cancelled order cannot be reopened. Create a new order if needed.',
                ]);
            }

            if ($oldStatus === 'delivered' && $newStatus !== 'delivered') {
                throw ValidationException::withMessages([
                    'status' => 'A delivered order cannot be moved to another fulfillment status.',
                ]);
            }

            if ($newStatus === 'delivered' && $oldStatus !== 'delivered'
                && ! $lockedOrder->shipments()->whereIn('status', ['shipped', 'in_transit', 'out_for_delivery'])->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'An order can be marked delivered only after its shipment has been dispatched.',
                ]);
            }

            if ($oldStatus === $newStatus) {
                return;
            }

            if ($newStatus === 'dispatched' && ! $lockedOrder->shipments()->where('status', 'shipped')->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Use the Dispatch form to record a carrier, tracking number and delivery partner before marking the order dispatched.',
                ]);
            }

            $timestamps = [];
            if ($newStatus === 'processing') {
                $timestamps['processing_at'] = now();
                if (! $lockedOrder->confirmed_at) {
                    $timestamps['confirmed_at'] = now();
                }
            } elseif ($newStatus === 'dispatched') {
                $timestamps['dispatched_at'] = now();
            } elseif ($newStatus === 'delivered') {
                $timestamps['delivered_at'] = now();
                $timestamps['completed_at'] = now();

                $shipment = $lockedOrder->shipments()->latest('id')->first();
                if ($shipment) {
                    $shipment->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                    ]);
                    $shipment->deliveryPartnerAssignments()
                        ->whereIn('status', ['assigned', 'accepted', 'picked_up', 'out_for_delivery'])
                        ->update(['status' => 'delivered', 'delivered_at' => now()]);
                    ShipmentTrackingEvent::create([
                        'shipment_id' => $shipment->id,
                        'event_code' => 'DELIVERED',
                        'event_status' => 'delivered',
                        'description' => 'Order marked as delivered by admin.',
                        'event_at' => now(),
                        'source' => 'admin',
                    ]);
                }
            } elseif ($newStatus === 'cancelled') {
                $timestamps['cancelled_at'] = now();
                $this->restoreInventoryForCancelledOrder($lockedOrder, $request->user()?->id);
                foreach ($lockedOrder->shipments()->whereNotIn('status', ['delivered', 'returned'])->get() as $shipment) {
                    $shipment->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                    $shipment->deliveryPartnerAssignments()
                        ->whereNotIn('status', ['delivered', 'cancelled'])
                        ->update(['status' => 'cancelled', 'cancelled_at' => now()]);
                }
            }

            $lockedOrder->update(array_merge([
                'status' => $newStatus,
                'fulfillment_status' => $newStatus,
            ], $timestamps));

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'changed_by_user_id' => $request->user()?->id,
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'source' => 'admin',
                'remarks' => $validated['notes'],
                'changed_at' => now(),
            ]);
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Restore inventory exactly once when an order is cancelled.
     * The sale transaction records identify which inventory locations were deducted.
     */
    private function restoreInventoryForCancelledOrder(Order $order, ?int $userId): void
    {
        $sales = InventoryTransaction::query()
            ->where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->where('transaction_type', 'sale')
            ->get()
            ->groupBy('inventory_id');

        foreach ($sales as $inventoryId => $transactions) {
            $alreadyRestored = InventoryTransaction::query()
                ->where('reference_type', Order::class)
                ->where('reference_id', $order->id)
                ->where('inventory_id', $inventoryId)
                ->where('transaction_type', 'return')
                ->exists();

            if ($alreadyRestored) {
                continue;
            }

            $inventory = Inventory::query()->lockForUpdate()->find($inventoryId);
            if (! $inventory) {
                continue;
            }

            $quantity = (int) $transactions->sum(fn ($transaction) => abs((int) $transaction->quantity_change));
            if ($quantity <= 0) {
                continue;
            }

            $before = (int) $inventory->quantity_on_hand;
            $inventory->quantity_on_hand += $quantity;
            $inventory->quantity_available += $quantity;
            $inventory->save();

            InventoryTransaction::create([
                'inventory_id' => $inventory->id,
                'performed_by_user_id' => $userId,
                'transaction_type' => 'return',
                'quantity_change' => $quantity,
                'quantity_before' => $before,
                'quantity_after' => (int) $inventory->quantity_on_hand,
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'reference_number' => $order->order_id,
                'reason' => 'Inventory restored after order cancellation.',
            ]);
        }
    }
}

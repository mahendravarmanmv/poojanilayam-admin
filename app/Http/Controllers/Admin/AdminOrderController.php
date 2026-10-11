<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Models\DeliveryPartnerProfile;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'address', 'statusHistories.changedBy', 'shipments']);
        $deliveryPartners = DeliveryPartnerProfile::where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'deliveryPartners'));
    }

    public function dispatch(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_partner_id' => 'required|exists:delivery_partner_profiles,id',
            'tracking_number'     => 'required|string|max:100',
            'shipping_notes'      => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($order, $validated) {
            Shipment::create([
                'order_id'            => $order->id,
                'delivery_partner_id' => $validated['delivery_partner_id'],
                'tracking_number'     => $validated['tracking_number'],
                'status'              => 'dispatched',
                'dispatched_at'       => now(),
            ]);

            $oldStatus = $order->status;
            $order->update(['status' => 'dispatched']);

            OrderStatusHistory::create([
                'order_id'      => $order->id,
                'from_status'   => $oldStatus,
                'to_status'     => 'dispatched',
                'changed_by'    => auth()->id(),
                'notes'         => 'Dispatched via Admin. Tracking: ' . $validated['tracking_number'],
            ]);
        });

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order marked as dispatched.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,dispatched,delivered,cancelled',
            'notes'  => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($order, $validated) {
            $oldStatus = $order->status;
            $order->update(['status' => $validated['status']]);

            OrderStatusHistory::create([
                'order_id'      => $order->id,
                'from_status'   => $oldStatus,
                'to_status'     => $validated['status'],
                'changed_by'    => auth()->id(),
                'notes'         => $validated['notes'],
            ]);
        });

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated.');
    }
}
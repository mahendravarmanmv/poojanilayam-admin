@extends('admin.layouts.app')

@section('title', 'Manage Order ' . $order->order_id)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
        <h4 class="fw-bold mb-1">Order {{ $order->order_id }}</h4>
        <div class="small text-muted">Reference: {{ $order->reference_id }}</div>
        <div class="small text-muted">Placed {{ optional($order->placed_at ?? $order->created_at)->format('d M Y, h:i A') }}</div>
    </div>
    <div class="text-end">
        <span class="badge text-bg-primary fs-6">{{ ucfirst($order->fulfillment_status ?: $order->status) }}</span>
        <div class="small mt-2">Payment: <strong>{{ ucfirst($order->payment_status ?? 'pending') }}</strong></div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam me-2 text-primary"></i>Ordered Products</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Unit Price</th><th>Qty</th><th class="text-end">Line Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                    <small class="text-muted">SKU: {{ $item->product_sku ?: $item->product?->sku ?: 'N/A' }}</small>
                                </td>
                                <td>{{ $order->currency_code }} {{ number_format((float) $item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="text-end fw-semibold">{{ $order->currency_code }} {{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No line items are attached to this order.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                <div class="row justify-content-end">
                    <div class="col-12 col-md-6">
                        <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><span>{{ $order->currency_code }} {{ number_format((float) $order->subtotal, 2) }}</span></div>
                        <div class="d-flex justify-content-between mb-1"><span>Discount</span><span>- {{ $order->currency_code }} {{ number_format((float) $order->discount_amount, 2) }}</span></div>
                        <div class="d-flex justify-content-between mb-1"><span>Tax</span><span>{{ $order->currency_code }} {{ number_format((float) $order->tax_amount, 2) }}</span></div>
                        <div class="d-flex justify-content-between mb-2"><span>Shipping</span><span>{{ $order->currency_code }} {{ number_format((float) $order->shipping_amount, 2) }}</span></div>
                        <hr>
                        <div class="d-flex justify-content-between fs-5 fw-bold"><span>Grand Total</span><span>{{ $order->currency_code }} {{ number_format((float) $order->total_amount, 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Delivery Address Snapshot</h6></div>
            <div class="card-body">
                @if ($shippingAddress)
                    <div class="fw-semibold">{{ $shippingAddress->name }}</div>
                    <div>{{ $shippingAddress->phone }}</div>
                    <div class="text-muted">
                        {{ $shippingAddress->address_line_1 }}
                        @if ($shippingAddress->address_line_2) , {{ $shippingAddress->address_line_2 }} @endif
                        @if ($shippingAddress->landmark) , {{ $shippingAddress->landmark }} @endif
                        <br>{{ $shippingAddress->city }}, {{ $shippingAddress->state }} {{ $shippingAddress->postal_code }}
                        <br>{{ $shippingAddress->country }}
                    </div>
                @else
                    <p class="text-muted mb-0">No delivery address snapshot is available.</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Status History</h6></div>
            <div class="card-body">
                @forelse ($order->statusHistories->sortByDesc('changed_at') as $history)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>{{ ucfirst($history->from_status ?: 'created') }} → {{ ucfirst($history->to_status) }}</strong>
                            <small class="text-muted">{{ optional($history->changed_at)->format('d M Y, h:i A') }}</small>
                        </div>
                        @if ($history->remarks)<div class="small text-muted mt-1">{{ $history->remarks }}</div>@endif
                        <small class="text-muted">By: {{ $history->changedBy?->name ?? ucfirst($history->source ?: 'system') }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No status history recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-truck me-2 text-primary"></i>Dispatch / Tracking</h6></div>
            <div class="card-body">
                @forelse ($order->shipments as $shipment)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between"><strong>{{ $shipment->carrier_name ?: 'Carrier' }}</strong><span class="badge text-bg-info">{{ ucfirst($shipment->status) }}</span></div>
                        <div class="small mt-2">Tracking: {{ $shipment->tracking_number ?: 'Not assigned' }}</div>
                        @if ($shipment->tracking_url)<a href="{{ $shipment->tracking_url }}" target="_blank" rel="noopener noreferrer">Track shipment</a>@endif
                        @foreach ($shipment->deliveryPartnerAssignments as $assignment)
                            <div class="small text-muted mt-1">Delivery partner: {{ $assignment->deliveryPartnerProfile?->display_name ?? 'Not available' }}</div>
                        @endforeach
                    </div>
                @empty
                    <p class="small text-muted">No shipment has been created yet.</p>
                @endforelse

                @if (!in_array($order->fulfillment_status ?: $order->status, ['delivered', 'cancelled'], true))
                    <form action="{{ route('admin.orders.dispatch', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="delivery_partner_id" class="form-label small fw-semibold">Verified Delivery Partner</label>
                            <select id="delivery_partner_id" name="delivery_partner_id" class="form-select" required>
                                <option value="">-- Choose partner --</option>
                                @foreach ($deliveryPartners as $partner)
                                    <option value="{{ $partner->id }}" @selected(old('delivery_partner_id') == $partner->id)>
                                        {{ $partner->display_name }}{{ $partner->mobile ? ' — '.$partner->mobile : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($deliveryPartners->isEmpty())<small class="text-muted">No active, verified delivery partners are available.</small>@endif
                        </div>
                        <div class="mb-3">
                            <label for="carrier_name" class="form-label small fw-semibold">Carrier Name (optional)</label>
                            <input id="carrier_name" name="carrier_name" class="form-control" maxlength="191" value="{{ old('carrier_name') }}" placeholder="Defaults to partner name">
                        </div>
                        <div class="mb-3">
                            <label for="tracking_number" class="form-label small fw-semibold">Tracking / AWB Number</label>
                            <input id="tracking_number" name="tracking_number" class="form-control" maxlength="191" value="{{ old('tracking_number') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="shipping_notes" class="form-label small fw-semibold">Dispatch Notes</label>
                            <textarea id="shipping_notes" name="shipping_notes" rows="2" maxlength="1000" class="form-control">{{ old('shipping_notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100" @disabled($deliveryPartners->isEmpty())>
                            <i class="bi bi-box-arrow-up-right me-1"></i> Save Dispatch Details
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="fw-bold mb-0"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Update Fulfillment Status</h6></div>
            <div class="card-body">
                @if (($order->fulfillment_status ?: $order->status) === 'cancelled')
                    <p class="text-muted small mb-0">Cancelled orders are terminal and cannot be reopened.</p>
                @elseif (($order->fulfillment_status ?: $order->status) === 'delivered')
                    <p class="text-muted small mb-0">This order has been delivered.</p>
                @else
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status" class="form-label small fw-semibold">New Status</label>
                            <select id="status" name="status" class="form-select" required>
                                @foreach (['pending', 'processing', 'dispatched', 'delivered', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->fulfillment_status ?: $order->status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label small fw-semibold">Reason / Internal Note</label>
                            <textarea id="notes" name="notes" rows="3" maxlength="1000" class="form-control" required>{{ old('notes') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

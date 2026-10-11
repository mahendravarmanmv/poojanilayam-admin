@extends('admin.layouts.app')
@section('title', 'Manage Order #' . ($order->order_number ?? $order->id))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Orders</a>
        <h4 class="fw-bold mb-0">Order #{{ $order->order_number ?? $order->id }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($order->status) }}</span>
</div>

<div class="row g-4">
    <!-- Left: Line Items & Shipping Details -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-box me-2 text-primary"></i>Ordered Samagri & Products</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Item</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->product->name ?? 'Samagri Item' }}</div>
                                <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                            </td>
                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light text-end py-3">
                <span class="fs-5 fw-bold">Grand Total: ₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2 text-primary"></i>Delivery Destination</h6>
            </div>
            <div class="card-body">
                <div class="fw-bold">{{ $order->user->name ?? 'Recipient' }}</div>
                <div class="text-muted small mt-1">
                    {{ $order->address->address_line_1 ?? 'Address not specified' }}<br>
                    {{ $order->address->city->name ?? '' }}, {{ $order->address->state->name ?? '' }} - {{ $order->address->postal_code ?? '' }}<br>
                    Phone: {{ $order->user->phone ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Logistics & Dispatch Form -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-truck me-2 text-primary"></i>Logistics Dispatch</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.dispatch', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Delivery Partner</label>
                        <select name="delivery_partner_id" class="form-select" required>
                            <option value="">-- Choose Courier --</option>
                            @foreach($deliveryPartners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->company_name ?? 'Partner #'.$partner->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">AWB / Tracking Number</label>
                        <input type="text" name="tracking_number" class="form-control" placeholder="E.g., DTDC12345678" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Notes</label>
                        <textarea name="shipping_notes" rows="2" class="form-control" placeholder="Optional comments"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-box-arrow-up-right me-1"></i> Mark as Dispatched</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-arrow-repeat me-2 text-primary"></i>Manual Lifecycle Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status Override</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'processing', 'dispatched', 'delivered', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason for Change</label>
                        <input type="text" name="notes" class="form-control" placeholder="Internal remarks" required>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
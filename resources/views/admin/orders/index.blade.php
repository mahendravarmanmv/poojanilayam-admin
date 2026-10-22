@extends('admin.layouts.app')

@section('title', 'E-Commerce Orders')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h4 class="fw-bold mb-1">E-Commerce Orders Directory</h4>
        <p class="text-muted mb-0 small">Orders placed by customers through the public Store.</p>
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

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2">
            <div class="col-md-6">
                <label class="visually-hidden" for="orderSearch">Search orders</label>
                <input id="orderSearch" type="search" name="search" value="{{ request('search') }}"
                       class="form-control" maxlength="100" placeholder="Search order #, customer, email or phone">
            </div>
            <div class="col-md-4">
                <label class="visually-hidden" for="orderStatus">Fulfillment status</label>
                <select id="orderStatus" name="status" class="form-select">
                    <option value="">All Fulfillment Statuses</option>
                    @foreach (['pending', 'processing', 'dispatched', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> Filter</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Fulfillment</th>
                    <th>Payment</th>
                    <th>Ordered Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    @php
                        $status = $order->fulfillment_status ?: $order->status;
                        $badge = match ($status) {
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'dispatched' => 'info',
                            'processing' => 'primary',
                            default => 'secondary',
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $order->order_id }}</span>
                            <small class="d-block text-muted">{{ $order->reference_id }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $order->customerProfile?->user?->name ?? 'Customer unavailable' }}</span>
                            <small class="d-block text-muted">{{ $order->customerProfile?->user?->email }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $order->items_count }} line items</span></td>
                        <td class="fw-bold text-success">{{ $order->currency_code }} {{ number_format((float) $order->total_amount, 2) }}</td>
                        <td><span class="badge text-bg-{{ $badge }}">{{ ucfirst($status) }}</span></td>
                        <td><span class="badge text-bg-light border text-dark">{{ ucfirst($order->payment_status ?? 'pending') }}</span></td>
                        <td>{{ optional($order->placed_at ?? $order->created_at)->format('d M Y, h:i A') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                Manage <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection

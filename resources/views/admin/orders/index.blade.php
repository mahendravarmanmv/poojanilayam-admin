@extends('admin.layouts.app')
@section('title', 'E-Commerce Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">E-Commerce Orders Directory</h4>
</div>

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by order #...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Fulfillment Statuses</option>
                    @foreach(['pending', 'processing', 'dispatched', 'delivered', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
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
                    <th>Status</th>
                    <th>Ordered Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                <tr>
                    <td class="fw-bold">{{ $o->order_number ?? '#ORD-'.$o->id }}</td>
                    <td>{{ $o->user->name ?? 'Guest User' }}</td>
                    <td><span class="badge bg-secondary">{{ $o->orderItems->count() }} line items</span></td>
                    <td class="fw-bold text-success">₹{{ number_format($o->total_amount, 2) }}</td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($o->status) }}</span></td>
                    <td>{{ $o->created_at->format('d M Y, h:i A') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-outline-primary">Process <i class="bi bi-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $orders->links() }}
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('title', 'Executive Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Overview & Operations</h3>
    <span class="text-muted small"><i class="bi bi-clock me-1"></i> {{ now()->format('D, M d, Y - h:i A') }}</span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-primary border-4 rounded-3 h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Pooja Bookings</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['total_bookings']) }}</div>
                <div class="small text-warning fw-medium mt-1"><i class="bi bi-hourglass-split"></i> {{ $stats['pending_bookings'] }} Pending Allocation</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-success border-4 rounded-3 h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Store Orders</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['total_orders']) }}</div>
                <div class="small text-primary fw-medium mt-1"><i class="bi bi-gear-fill"></i> {{ $stats['processing_orders'] }} Processing</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4 rounded-3 h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Pujari Verifications</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['pending_pujaris']) }}</div>
                <div class="small text-muted mt-1"><i class="bi bi-exclamation-circle"></i> Awaiting KYC Review</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 border-start border-info border-4 rounded-3 h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Support Queue</div>
                <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['open_tickets']) }}</div>
                <div class="small text-danger fw-medium mt-1"><i class="bi bi-envelope-open"></i> Open Customer Inquiries</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings Table -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar2-week me-2 text-primary"></i>Recent Pooja Bookings</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Number</th>
                            <th>Customer</th>
                            <th>Pooja</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings as $b)
                        <tr>
                            <td class="fw-bold small">{{ $b->booking_number ?? '#BK-'.$b->id }}</td>
                            <td>{{ $b->user->name ?? 'Guest' }}</td>
                            <td>{{ $b->pooja->name ?? 'Custom Service' }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($b->status) }}</span></td>
                            <td><a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">No recent bookings recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-bag-check me-2 text-success"></i>Recent Store Orders</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Order No</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                        <tr>
                            <td class="fw-bold small">{{ $o->order_number ?? '#ORD-'.$o->id }}</td>
                            <td>₹{{ number_format($o->total_amount, 2) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($o->status) }}</span></td>
                            <td><a href="{{ route('admin.orders.show', $o) }}" class="btn btn-sm btn-light border"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted">No store orders recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('title', 'Pooja Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Pooja Bookings Directory</h4>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-2">
            <div class="col-12 col-md-4">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by booking #, customer, phone...">
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Booking Types</option>
                    <option value="home" {{ request('type') === 'home' ? 'selected' : '' }}>Home Pooja</option>
                    <option value="temple" {{ request('type') === 'temple' ? 'selected' : '' }}>Temple Pooja</option>
                    <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital Pooja</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Bookings List -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Booking #</th>
                    <th>Customer</th>
                    <th>Pooja Name</th>
                    <th>Type</th>
                    <th>Assigned Pujari</th>
                    <th>Status</th>
                    <th>Scheduled For</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td class="fw-bold">{{ $booking->booking_number ?? '#BK-'.$booking->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $booking->user->name ?? 'Guest User' }}</div>
                        <div class="text-muted small">{{ $booking->user->phone ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $booking->pooja->name ?? 'Standard Pooja' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($booking->booking_type ?? 'home') }}</span></td>
                    <td>
                        @if($booking->pujari)
                            <span class="text-success fw-medium"><i class="bi bi-person-check me-1"></i>{{ $booking->pujari->user->name ?? 'Assigned' }}</span>
                        @else
                            <span class="text-danger small"><i class="bi bi-exclamation-triangle me-1"></i>Unassigned</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($booking->status) }}</span></td>
                    <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : 'TBD' }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                            Manage <i class="bi bi-arrow-right"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No bookings found matching criteria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
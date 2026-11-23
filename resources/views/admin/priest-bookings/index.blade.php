
@extends('admin.layouts.app')
@section('title', 'Priest Bookings')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Priest Bookings Directory</h4>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.priest-bookings.index') }}" class="row g-2">

            <div class="col-12 col-md-4">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control"
                       placeholder="Search booking #, customer, email, phone...">
            </div>

            <div class="col-6 col-md-3">
                <select name="booking_status" class="form-select">
                    <option value="">All Booking Statuses</option>
                    @foreach(['pending', 'confirmed', 'completed', 'cancelled'] as $st)
                        <option value="{{ $st }}" {{ request('booking_status') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3">
                <select name="payment_status" class="form-select">
                    <option value="">All Payment Statuses</option>
                    @foreach(['pending', 'paid', 'failed', 'refunded'] as $st)
                        <option value="{{ $st }}" {{ request('payment_status') === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
            </div>

        </form>
    </div>
</div>

<!-- Priest Bookings List -->
<div class="card border-0 shadow-sm">

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">

            <thead class="table-light">
                <tr>
                    <th>Booking #</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Scheduled For</th>
                    <th>Amount</th>
                    <th>Booking Status</th>
                    <th>Payment</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($bookings as $booking)

                    <tr>

                        <td class="fw-bold">
                            {{ $booking->booking_number ?? '#PB-'.$booking->id }}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $booking->first_name }} {{ $booking->last_name }}
                            </div>
                            <div class="text-muted small">
                                {{ $booking->mobile ?? 'N/A' }}
                            </div>
                            <div class="text-muted small">
                                {{ $booking->email ?? '' }}
                            </div>
                        </td>

                        <td>
                            {{ $booking->service_name ?? 'Priest Service' }}
                        </td>

                        <td>
                            <div>
                                {{ $booking->booking_date
                                    ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y')
                                    : 'TBD' }}
                            </div>
                            <small class="text-muted">
                                {{ $booking->start_time ?? '' }}
                                @if($booking->end_time)
                                    - {{ $booking->end_time }}
                                @endif
                            </small>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                ₹{{ number_format($booking->total_amount ?? 0, 2) }}
                            </div>
                        </td>

                        <td>
                            @php
                                $bookingStatusClass = match($booking->booking_status) {
                                    'confirmed' => 'bg-success',
                                    'completed' => 'bg-primary',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp

                            <span class="badge {{ $bookingStatusClass }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->booking_status ?? 'pending')) }}
                            </span>
                        </td>

                        <td>
                            @php
                                $paymentStatusClass = match($booking->payment_status) {
                                    'paid' => 'bg-success',
                                    'failed' => 'bg-danger',
                                    'refunded' => 'bg-secondary',
                                    default => 'bg-warning text-dark',
                                };
                            @endphp

                            <span class="badge {{ $paymentStatusClass }}">
                                {{ ucfirst($booking->payment_status ?? 'pending') }}
                            </span>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('admin.priest-bookings.show', $booking) }}"
                               class="btn btn-sm btn-outline-primary">
                                Manage <i class="bi bi-arrow-right"></i>
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No priest bookings found matching criteria.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

    <div class="card-footer bg-white py-3">
        {{ $bookings->links() }}
    </div>

</div>

@endsection

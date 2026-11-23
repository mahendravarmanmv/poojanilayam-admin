
@extends('admin.layouts.app')
@section('title', 'Manage Priest Booking #' . ($priestBooking->booking_number ?? $priestBooking->id))

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.priest-bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Back to Priest Bookings
        </a>

        <h4 class="fw-bold mb-0">
            Booking #{{ $priestBooking->booking_number ?? $priestBooking->id }}
        </h4>
    </div>

    <div class="d-flex flex-column gap-2 align-items-end">
        <span class="badge bg-primary fs-6 px-3 py-2">
            {{ ucfirst(str_replace('_', ' ', $priestBooking->booking_status ?? 'pending')) }}
        </span>

        <span class="badge bg-success fs-6 px-3 py-2">
            Payment: {{ ucfirst($priestBooking->payment_status ?? 'pending') }}
        </span>
    </div>
</div>

<div class="row g-4">

    <!-- Left Column: Booking Details -->
    <div class="col-12 col-lg-8">

        <!-- Service & Customer Details -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-info-circle me-2 text-primary"></i>
                    Service & Customer Details
                </h6>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="text-muted small">Service</label>
                        <div class="fw-bold">
                            {{ $priestBooking->service_name ?? 'Priest Service' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Customer Name</label>
                        <div class="fw-bold">
                            {{ $priestBooking->first_name }} {{ $priestBooking->last_name }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Email</label>
                        <div class="fw-bold">
                            {{ $priestBooking->email ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Mobile</label>
                        <div class="fw-bold">
                            {{ $priestBooking->mobile ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Booking Date</label>
                        <div class="fw-bold">
                            {{ $priestBooking->booking_date
                                ? \Carbon\Carbon::parse($priestBooking->booking_date)->format('d M Y')
                                : 'Not set' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Scheduled Time</label>
                        <div class="fw-bold">
                            {{ $priestBooking->start_time ?? 'N/A' }}
                            @if($priestBooking->end_time)
                                - {{ $priestBooking->end_time }}
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Duration</label>
                        <div class="fw-bold">
                            {{ $priestBooking->duration_minutes ?? 0 }} minutes
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Booking Number</label>
                        <div class="fw-bold">
                            {{ $priestBooking->booking_number ?? '#PB-'.$priestBooking->id }}
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small">Service Address</label>

                        <div class="p-2 bg-light rounded small">
                            {{ $priestBooking->address ?? 'N/A' }}

                            @if($priestBooking->city)
                                , {{ $priestBooking->city }}
                            @endif

                            @if($priestBooking->state)
                                , {{ $priestBooking->state }}
                            @endif

                            @if($priestBooking->pincode)
                                - {{ $priestBooking->pincode }}
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Booking & Service Information -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-calendar-check me-2 text-primary"></i>
                    Booking Information
                </h6>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="text-muted small">Service Name</label>
                        <div class="fw-bold">
                            {{ $priestBooking->service_name ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Service Amount</label>
                        <div class="fw-bold">
                            ₹{{ number_format($priestBooking->service_amount ?? 0, 2) }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Tax Amount</label>
                        <div class="fw-bold">
                            ₹{{ number_format($priestBooking->tax_amount ?? 0, 2) }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small">Total Amount</label>
                        <div class="fw-bold text-success fs-5">
                            ₹{{ number_format($priestBooking->total_amount ?? 0, 2) }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Tax Details -->
        @if(!empty($priestBooking->tax_details))
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-receipt me-2 text-primary"></i>
                        Tax Details
                    </h6>
                </div>

                <div class="card-body">

                    @if(is_array($priestBooking->tax_details))

                        <pre class="bg-light p-3 rounded small mb-0">{{ json_encode($priestBooking->tax_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>

                    @else

                        <div>{{ $priestBooking->tax_details }}</div>

                    @endif

                </div>
            </div>
        @endif

        <!-- Payment History -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-credit-card me-2 text-primary"></i>
                    Payment History
                </h6>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Gateway</th>
                                <th>Payment ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Paid At</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($priestBooking->payments as $payment)

                                <tr>

                                    <td>
                                        {{ ucfirst($payment->gateway ?? 'N/A') }}
                                    </td>

                                    <td>
                                        <small>
                                            {{ $payment->gateway_payment_id ?? $payment->gateway_order_id ?? 'N/A' }}
                                        </small>
                                    </td>

                                    <td class="fw-semibold">
                                        ₹{{ number_format($payment->amount ?? 0, 2) }}
                                    </td>

                                    <td>
                                        @php
                                            $paymentBadge = match($payment->payment_status) {
                                                'paid', 'success' => 'bg-success',
                                                'failed' => 'bg-danger',
                                                'refunded' => 'bg-secondary',
                                                default => 'bg-warning text-dark',
                                            };
                                        @endphp

                                        <span class="badge {{ $paymentBadge }}">
                                            {{ ucfirst($payment->payment_status ?? 'pending') }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $payment->paid_at
                                            ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y, h:i A')
                                            : 'N/A' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No payment records found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>

    <!-- Right Column: Status & Summary -->
    <div class="col-12 col-lg-4">

        <!-- Booking Summary -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clipboard-check me-2 text-primary"></i>
                    Booking Summary
                </h6>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Booking Status</span>
                    <span class="fw-semibold">
                        {{ ucfirst(str_replace('_', ' ', $priestBooking->booking_status ?? 'pending')) }}
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Payment Status</span>
                    <span class="fw-semibold">
                        {{ ucfirst($priestBooking->payment_status ?? 'pending') }}
                    </span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Service Amount</span>
                    <span>₹{{ number_format($priestBooking->service_amount ?? 0, 2) }}</span>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tax</span>
                    <span>₹{{ number_format($priestBooking->tax_amount ?? 0, 2) }}</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total</span>
                    <span class="fw-bold text-success fs-5">
                        ₹{{ number_format($priestBooking->total_amount ?? 0, 2) }}
                    </span>
                </div>

            </div>
        </div>

        <!-- Booking Metadata -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-clock-history me-2 text-primary"></i>
                    Booking Timeline
                </h6>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="text-muted small">Booking Created</label>
                    <div class="fw-semibold">
                        {{ $priestBooking->created_at
                            ? $priestBooking->created_at->format('d M Y, h:i A')
                            : 'N/A' }}
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Last Updated</label>
                    <div class="fw-semibold">
                        {{ $priestBooking->updated_at
                            ? $priestBooking->updated_at->format('d M Y, h:i A')
                            : 'N/A' }}
                    </div>
                </div>

                @if($priestBooking->payment_expires_at)
                    <div>
                        <label class="text-muted small">Payment Expiry</label>
                        <div class="fw-semibold">
                            {{ $priestBooking->payment_expires_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>

@endsection

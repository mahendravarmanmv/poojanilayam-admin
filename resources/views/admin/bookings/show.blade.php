@extends('admin.layouts.app')
@section('title', 'Manage Booking #' . ($booking->booking_number ?? $booking->id))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Bookings</a>
        <h4 class="fw-bold mb-0">Booking #{{ $booking->booking_number ?? $booking->id }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($booking->status) }}</span>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Sankalpam -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Service & Customer Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Pooja Service</label>
                        <div class="fw-bold">{{ $booking->pooja->name ?? 'Standard Pooja' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Customer Name</label>
                        <div class="fw-bold">{{ $booking->user->name ?? 'N/A' }} ({{ $booking->user->phone ?? 'N/A' }})</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Scheduled Date & Slot</label>
                        <div class="fw-bold">{{ $booking->booking_date ?? 'Not set' }} {{ $booking->booking_slot ?? '' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Total Cost</label>
                        <div class="fw-bold text-success">₹{{ number_format($booking->total_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small">Service Location Address</label>
                        <div class="p-2 bg-light rounded small">{{ $booking->address->address_line_1 ?? 'No physical address associated (Online/Temple)' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sankalpam Members -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Sankalpam Details</h6>
            </div>
            <div class="card-body">
                @if($booking->sankalpam)
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Gothram:</strong> {{ $booking->sankalpam->gothram ?? 'N/A' }}</div>
                        <div class="col-md-6"><strong>Primary Nakshatram:</strong> {{ $booking->sankalpam->nakshatram ?? 'N/A' }}</div>
                    </div>
                    <h6>Family Members Included:</h6>
                    <ul class="list-group list-group-flush small">
                        @forelse($booking->sankalpam->members as $member)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>{{ $member->name }} ({{ $member->relationship ?? 'Member' }})</span>
                                <span class="text-muted">{{ $member->rashi ?? '' }} | {{ $member->nakshatram ?? '' }}</span>
                            </li>
                        @empty
                            <li class="list-group-item px-0 text-muted">No family members registered.</li>
                        @endforelse
                    </ul>
                @else
                    <p class="text-muted small mb-0">No Sankalpam submitted for this booking.</p>
                @endif
            </div>
        </div>

        <!-- Status History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Activity & Status Timeline</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    @forelse($booking->statusHistories as $history)
                        <li class="list-group-item py-3">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold text-dark">{{ ucfirst($history->from_status) }} &rarr; {{ ucfirst($history->to_status) }}</span>
                                <span class="text-muted">{{ $history->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="text-muted mt-1">{{ $history->change_reason }}</div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted py-3">No activity logs recorded yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Pujari Assignment & Actions -->
    <div class="col-12 col-lg-4">
        <!-- Assignment Form -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Assign / Reassign Pujari</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.assign', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Select Certified Pujari</label>
                        <select name="pujari_id" class="form-select" required>
                            <option value="">-- Choose Pujari --</option>
                            @foreach($availablePujaris as $pujari)
                                <option value="{{ $pujari->id }}" {{ $booking->pujari_id == $pujari->id ? 'selected' : '' }}>
                                    {{ $pujari->user->name ?? 'Pujari #'.$pujari->id }} ({{ $pujari->experience_years ?? '0' }} yrs exp)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Internal Notes for Pujari</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="Special requirements, address tips..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i> Save Assignment</button>
                </form>
            </div>
        </div>

        <!-- Manual Status Override -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-sliders me-2 text-primary"></i>Status Transition</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">New Lifecycle Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'confirmed', 'assigned', 'in_progress', 'completed', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ $booking->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason for Transition</label>
                        <input type="text" name="reason" class="form-control" placeholder="E.g., Verified phone call, customer requested reschedule" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 text-dark fw-medium">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('title', 'Manage Astrology Session')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.astrology.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Astrology List</a>
        <h4 class="fw-bold mb-0">Consultation Session #{{ $booking->booking_number ?? $booking->id }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($booking->status) }}</span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Session Details</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Customer:</strong> {{ $booking->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Astrologer:</strong> {{ $booking->astrologer->user->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Service Category:</strong> {{ $booking->service->name ?? 'General Horoscope' }}</div>
                    <div class="col-md-6"><strong>Scheduled At:</strong> {{ $booking->scheduled_at ?? 'Pending Slot Confirmation' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Lifecycle Override</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.astrology.status', $booking) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'] as $st)
                                <option value="{{ $st }}" {{ $booking->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Reason</label>
                        <input type="text" name="reason" class="form-control" required placeholder="Reason for status change">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
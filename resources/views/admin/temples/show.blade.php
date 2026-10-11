@extends('admin.layouts.app')
@section('title', 'Manage Temple: ' . $temple->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.temples.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Temples</a>
        <h4 class="fw-bold mb-0">{{ $temple->name }}</h4>
    </div>
    <span class="badge {{ $temple->is_verified ? 'bg-success' : 'bg-warning text-dark' }} fs-6 px-3 py-2">
        {{ $temple->is_verified ? 'Verified' : 'Pending Verification' }}
    </span>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Temple Information</h6>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ $temple->profile->description ?? 'No detailed description available.' }}</p>
                <div class="row g-2 small">
                    <div class="col-md-6"><strong>Address:</strong> {{ $temple->profile->address ?? 'N/A' }}</div>
                    <div class="col-md-6"><strong>Established:</strong> {{ $temple->profile->established_year ?? 'Historical' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-patch-check me-2 text-primary"></i>Verification Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.temples.verify', $temple) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="verified" {{ $temple->is_verified ? 'selected' : '' }}>Verified</option>
                            <option value="rejected">Rejected</option>
                            <option value="pending" {{ !$temple->is_verified ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Remarks</label>
                        <textarea name="remarks" rows="3" class="form-control" required placeholder="Verification notes"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
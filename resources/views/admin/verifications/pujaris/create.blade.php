@extends('admin.layouts.app')
@section('title', 'Add New Priest')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Add New Priest / Pujari</h4>
    <a href="{{ route('admin.verifications.pujaris.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Priests
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.verifications.pujaris.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="E.g., Sharma Shastri" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="priest@poojanilayam.com" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}" placeholder="9876543210" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Login Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Experience (Years)</label>
                    <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years', 5) }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Primary Temple Affiliation</label>
                    <select name="temple_id" class="form-select">
                        <option value="">Independent / Freelance</option>
                        @foreach($temples as $temple)
                            <option value="{{ $temple->id }}" {{ old('temple_id') == $temple->id ? 'selected' : '' }}>{{ $temple->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Initial Verification Status</label>
                    <select name="verification_status" class="form-select" required>
                        <option value="verified" selected>Verified</option>
                        <option value="pending">Pending Review</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Bio / Ritual Background</label>
                    <textarea name="about" rows="3" class="form-control" placeholder="Specialization in Vedic rituals, Gothram expertise, Homam certifications...">{{ old('about') }}</textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Create Priest</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
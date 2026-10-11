@extends('admin.layouts.app')
@section('title', 'Edit Priest Profile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Edit Priest: {{ $pujari->user->name ?? 'Priest #'.$pujari->id }}</h4>
    <a href="{{ route('admin.verifications.pujaris.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Priests
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.verifications.pujaris.update', $pujari) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $pujari->user->name ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $pujari->user->email ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $pujari->user->mobile ?? '') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Experience (Years)</label>
                    <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years', $pujari->experience_years) }}" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Temple Affiliation</label>
                    <select name="temple_id" class="form-select">
                        <option value="">Independent / Freelance</option>
                        @foreach($temples as $temple)
                            <option value="{{ $temple->id }}" {{ ($pujari->templeAssignments->first()->temple_id ?? null) == $temple->id ? 'selected' : '' }}>
                                {{ $temple->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Verification Status</label>
                    <select name="verification_status" class="form-select" required>
                        @foreach(['pending', 'verified', 'rejected', 'suspended'] as $st)
                            <option value="{{ $st }}" {{ $pujari->verification_status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Bio / Ritual Background</label>
                    <textarea name="about" rows="3" class="form-control">{{ old('about', $pujari->about) }}</textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Priest</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
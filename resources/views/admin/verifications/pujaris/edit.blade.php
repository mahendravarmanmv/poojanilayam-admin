@extends('admin.layouts.app')

@section('title', 'Edit Priest')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">Edit Priest: {{ $pujari->display_name }}</h4>
            <span class="text-muted small">ID: {{ $pujari->pujari_number }}</span>
        </div>
        <a href="{{ route('admin.verifications.pujaris.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Back to Priests
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.verifications.pujaris.update', $pujari->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Legal Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $pujari->user?->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Public Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $pujari->display_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $pujari->user?->email) }}" required>
                    </div>

					<div class="col-md-6">
					<label class="form-label fw-semibold">Phone Number</label>
					<input type="text" name="phone" class="form-control" value="{{ old('phone', $phone) }}" placeholder="+91 9876543210">
					</div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Years of Experience <span class="text-danger">*</span></label>
                        <input type="number" name="experience_years" class="form-control" value="{{ old('experience_years', $pujari->experience_years) }}" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Primary Specialization</label>
                        <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $specialization) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Verification Status <span class="text-danger">*</span></label>
                        <select name="verification_status" class="form-select" required>
                            <option value="approved" {{ old('verification_status', $pujari->verification_status) === 'approved' ? 'selected' : '' }}>Approved (Visible on Website)</option>
                            <option value="pending" {{ old('verification_status', $pujari->verification_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('verification_status', $pujari->verification_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Profile Photo</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        @if ($pujari->user?->profile?->profile_photo)
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="https://poojanilayam.co.in/{{ $pujari->user->profile->profile_photo }}" alt="Preview" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                                <span class="text-muted small">{{ $pujari->user->profile->profile_photo }}</span>
                            </div>
                        @endif
                    </div>

					<div class="col-12">
					<label class="form-label fw-semibold">Languages Known</label>
					<div class="d-flex flex-wrap gap-3">
					@forelse ($languages as $lang)
					<div class="form-check">
					<input class="form-check-input" type="checkbox" name="languages[]" value="{{ $lang->id }}" id="lang_{{ $lang->id }}" {{ in_array($lang->id, old('languages', $selectedLanguages)) ? 'checked' : '' }}>
					<label class="form-check-label" for="lang_{{ $lang->id }}">{{ $lang->name }}</label>
					</div>
					@empty
					<span class="text-muted small">No languages configured. Run the language seed query in phpMyAdmin.</span>
					@endforelse
					</div>
					</div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Bio / Profile Description</label>
                        <textarea name="bio" rows="4" class="form-control">{{ old('bio', $pujari->bio) }}</textarea>
                    </div>

                    <div class="col-12">
    <div class="form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ (old('is_active') !== null ? old('is_active') == '1' : $pujari->is_active) ? 'checked' : '' }}>
        <label class="form-check-label fw-semibold" for="is_active">Active & Available in Directory</label>
    </div>
</div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Add Priest Service')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Add New Service</h4>
        <p class="text-muted mb-0">
            Add a service for {{ $pujari->user->name ?? 'Priest #'.$pujari->id }}
        </p>
    </div>

    <a href="{{ route('admin.verifications.pujaris.services.index', $pujari->id) }}"
       class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Services
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form method="POST"
              action="{{ route('admin.verifications.pujaris.services.store', $pujari->id) }}">

            @csrf

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Service Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="service_name"
                           class="form-control @error('service_name') is-invalid @enderror"
                           value="{{ old('service_name') }}"
                           placeholder="e.g. Vedic Pooja"
                           required>

                    @error('service_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Service Price (₹) <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="price"
                           class="form-control @error('price') is-invalid @enderror"
                           value="{{ old('price') }}"
                           min="0"
                           step="0.01"
                           placeholder="e.g. 1500"
                           required>

                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
				
				<div class="col-md-6">
				<label class="form-label fw-semibold">
				Service Duration (Minutes) <span class="text-danger">*</span>
				</label>

				<input type="number"
				name="duration_minutes"
				class="form-control @error('duration_minutes') is-invalid @enderror"
				value="{{ old('duration_minutes', 60) }}"
				min="5"
				max="1440"
				step="5"
				placeholder="e.g. 60"
				required>

				@error('duration_minutes')
				<div class="invalid-feedback">{{ $message }}</div>
				@enderror

				<small class="text-muted">Enter the expected service duration in minutes.</small>
				</div>

                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Service Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Enter a short description of this service">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Display Order
                    </label>

                    <input type="number"
                           name="sort_order"
                           class="form-control @error('sort_order') is-invalid @enderror"
                           value="{{ old('sort_order', 0) }}"
                           min="0">

                    @error('sort_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="text-muted">
                        Lower numbers appear first.
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">
                        Service Status
                    </label>

                    <div class="form-check form-switch mt-2">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               class="form-check-input"
                               id="is_active"
                               {{ old('is_active', '1') ? 'checked' : '' }}>

                        <label class="form-check-label" for="is_active">
                            Active & Available for Booking
                        </label>
                    </div>
                </div>

            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.verifications.pujaris.services.index', $pujari->id) }}"
                   class="btn btn-outline-secondary">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Save Service
                </button>
            </div>

        </form>

    </div>
</div>

@endsection

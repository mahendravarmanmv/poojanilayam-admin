
@extends('admin.layouts.app')

@section('title', 'Create Pooja Deity')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Pooja Deity</h1>
            <p class="text-muted small mb-0">Add a new deity to the Pooja catalog.</p>
        </div>

        <a href="{{ route('admin.gods.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Deities
        </a>
    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Deity Information</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.gods.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Deity Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g., Lord Ganesha"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Short Description
                        </label>

                        <textarea name="short_description"
                                  rows="2"
                                  maxlength="500"
                                  class="form-control @error('short_description') is-invalid @enderror"
                                  placeholder="Enter a short description...">{{ old('short_description') }}</textarea>

                        @error('short_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter deity details...">{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sort Order</label>

                        <input type="number"
                               name="sort_order"
                               min="0"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', 0) }}">

                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold d-block">Featured</label>

                        <input type="hidden" name="is_featured" value="0">

                        <div class="form-check form-switch mt-2">
                            <input type="checkbox"
                                   name="is_featured"
                                   value="1"
                                   class="form-check-input"
                                   id="is_featured"
                                   {{ old('is_featured') ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_featured">
                                Mark as featured
                            </label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold d-block">Status</label>

                        <input type="hidden" name="is_active" value="0">

                        <div class="form-check form-switch mt-2">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   class="form-check-input"
                                   id="is_active"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>

                        @error('is_active')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Create Deity
                    </button>

                    <a href="{{ route('admin.gods.index') }}"
                       class="btn btn-light border">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection


@extends('admin.layouts.app')

@section('title', 'Edit Pooja Category')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Pooja Category</h1>
            <p class="text-muted small mb-0">Update existing category information.</p>
        </div>

        <a href="{{ route('admin.pooja-categories.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Categories
        </a>
    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Category Information</h5>
        </div>

        <div class="card-body">

            <form action="{{ route('admin.pooja-categories.update', $category) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Category Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $category->name) }}"
                               required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Description
                        </label>

                        <textarea name="description"
                                  rows="4"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Category Code</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="{{ $category->category_code }}"
                               readonly>
                        <small class="text-muted">
                            Category code cannot be changed.
                        </small>
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Update Category
                    </button>

                    <a href="{{ route('admin.pooja-categories.index') }}"
                       class="btn btn-light border">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

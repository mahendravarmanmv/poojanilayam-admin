
@extends('admin.layouts.app')

@section('title', 'Create Pooja Category')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Pooja Category</h1>
            <p class="text-muted small mb-0">Add a new category to the Pooja catalog.</p>
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

            <form action="{{ route('admin.pooja-categories.store') }}"
                  method="POST">
                @csrf

                <div class="row g-3">

                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Category Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="e.g., Ganesh Pooja"
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
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Enter category description...">{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Create Category
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

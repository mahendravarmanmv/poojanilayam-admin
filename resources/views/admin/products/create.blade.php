@extends('admin.layouts.app')
@section('title', 'Add Store Product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Add Store Product</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Products</a>
</div>
@if($categories->isEmpty())
    <div class="alert alert-warning">There are no active product categories yet. Create or activate a category before adding a product.</div>
@endif
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form')
        <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-primary" @disabled($categories->isEmpty())><i class="bi bi-save me-1"></i> Save Product</button><a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a></div>
    </form>
</div></div>
@endsection

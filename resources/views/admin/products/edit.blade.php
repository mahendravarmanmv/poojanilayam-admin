@extends('admin.layouts.app')
@section('title', 'Edit Store Product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Products</a>
</div>
<div class="card border-0 shadow-sm"><div class="card-body p-4">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form')
        <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update Product</button><a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a></div>
    </form>
</div></div>
@endsection

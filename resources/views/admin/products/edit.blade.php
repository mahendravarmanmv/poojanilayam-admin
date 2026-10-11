@extends('admin.layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Edit Product: {{ $product->name }}</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="product_category_id" class="form-select" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->product_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Brand</label>
                    <select name="brand_id" class="form-select">
                        <option value="">None</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock Quantity On Hand</label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->inventory->quantity_on_hand ?? 0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sale Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price->price ?? 0) }}" required>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" {{ $product->is_active ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="isActive">Product Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="4" class="form-control" required>{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Product</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
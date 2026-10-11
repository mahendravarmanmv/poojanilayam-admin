@extends('admin.layouts.app')
@section('title', 'Add Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Add New Samagri Item</h4>
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Products</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('admin.products.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Category</label>
                    <select name="product_category_id" class="form-select" required>
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Brand / Manufacturer</label>
                    <select name="brand_id" class="form-select">
                        <option value="">None / Pooja Nilayam In-House</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="E.g., SMG-001" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Initial Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 100) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Sale Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive" checked>
                        <label class="form-check-label fw-semibold" for="isActive">Available immediately in store</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Product Description & Samagri Usage</label>
                    <textarea name="description" rows="4" class="form-control" required>{{ old('description') }}</textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Save Product</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
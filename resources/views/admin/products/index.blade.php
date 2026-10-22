@extends('admin.layouts.app')
@section('title', 'Store Product Catalog')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div><h4 class="fw-bold mb-1">Store Products & Inventory</h4><p class="text-muted mb-0">Manage products shown on the public Store page.</p></div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
</div>
@if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card border-0 shadow-sm mb-3"><div class="card-body">
    <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2">
        <div class="col-lg-5"><input type="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search product name or SKU"></div>
        <div class="col-lg-3"><select name="category_id" class="form-select"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-lg-2"><select name="status" class="form-select"><option value="">All statuses</option><option value="active" @selected(request('status') === 'active')>Active</option><option value="inactive" @selected(request('status') === 'inactive')>Inactive</option></select></div>
        <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit">Filter</button><a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Clear</a></div>
    </form>
</div></div>
<div class="card border-0 shadow-sm"><div class="table-responsive">
<table class="table table-hover align-middle mb-0"><thead class="table-light"><tr><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Available stock</th><th>Store status</th><th class="text-end">Actions</th></tr></thead><tbody>
@forelse($products as $product)
@php($price = $product->prices->first())
@php($stock = $product->inventories->sum('quantity_available'))
<tr>
    <td><div class="d-flex align-items-center gap-2">@if($product->images->first())<img src="{{ rtrim(config('store.frontend_url', 'https://poojanilayam.co.in'), '/') . '/' . ltrim($product->images->first()->file_path, '/') }}" alt="{{ $product->name }}" style="width:48px;height:48px;object-fit:cover" class="rounded border" loading="lazy" onerror="this.style.display='none'">@else<div class="rounded border bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px"><i class="bi bi-image text-muted"></i></div>@endif<div><div class="fw-semibold">{{ $product->name }}</div><small class="text-muted">{{ $product->featured ? 'Featured' : 'Standard' }}</small></div></div></td>
    <td>{{ $product->category?->name ?? 'Uncategorized' }}</td><td><code>{{ $product->sku }}</code></td>
    <td class="fw-semibold">{{ $price ? '₹'.number_format((float) $price->amount, 2) : 'Price not set' }}</td>
    <td><span class="badge {{ $stock > 0 ? 'bg-success' : 'bg-danger' }}">{{ number_format($stock) }} available</span></td>
    <td><span class="badge {{ $product->active ? 'bg-success' : 'bg-secondary' }}">{{ $product->active ? 'Active' : 'Hidden' }}</span></td>
    <td class="text-end text-nowrap"><a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" aria-label="Edit {{ $product->name }}"><i class="bi bi-pencil"></i> Edit</a>
        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this product? It will be removed from the public Store.');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Archive {{ $product->name }}"><i class="bi bi-archive"></i></button></form>
    </td>
</tr>
@empty<tr><td colspan="7" class="text-center py-5 text-muted">No products found. Add a product to populate the public Store.</td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $products->links() }}</div></div>
@endsection

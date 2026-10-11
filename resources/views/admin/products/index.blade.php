@extends('admin.layouts.app')
@section('title', 'Product Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">E-Commerce Products & Inventory</h4>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock On Hand</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr>
                    <td class="fw-bold">{{ $p->name }}</td>
                    <td>{{ $p->category->name ?? 'Uncategorized' }}</td>
                    <td><code>{{ $p->sku }}</code></td>
                    <td class="fw-bold text-success">₹{{ number_format($p->price->price ?? 0, 2) }}</td>
                    <td>
                        <span class="badge {{ ($p->inventory->quantity_on_hand ?? 0) < 10 ? 'bg-danger' : 'bg-success' }}">
                            {{ $p->inventory->quantity_on_hand ?? 0 }} units
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $p->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $p->is_active ? 'Active' : 'Disabled' }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No products cataloged.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $products->links() }}
    </div>
</div>
@endsection
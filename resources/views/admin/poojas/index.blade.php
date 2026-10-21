@extends('admin.layouts.app')

@section('title', 'Pooja Catalog Management')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Pooja Catalog</h1>
            <p class="text-muted small mb-0">Manage temple rituals, community ceremonies, pricing, and samagri requirements.</p>
        </div>
        <a href="{{ route('admin.poojas.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Add New Pooja</span>
        </a>
    </div>

   

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.poojas.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Search pooja name..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="filter_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="popular" {{ request('filter_type') == 'popular' ? 'selected' : '' }}>Popular</option>
                        <option value="temple" {{ request('filter_type') == 'temple' ? 'selected' : '' }}>Temple Poojas</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary w-100" title="Apply Filters">Filter</button>
                    @if(request()->hasAny(['search', 'category_id', 'status', 'filter_type']))
                        <a href="{{ route('admin.poojas.index') }}" class="btn btn-outline-light text-dark border" title="Reset Filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-muted">
                    <tr>
                        <th scope="col" class="ps-4" style="width: 70px;">Image</th>
                        <th scope="col">Name & Category</th>
                        <th scope="col">Pricing Tiers</th>
                        <th scope="col">Tags</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end pe-4" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poojas as $pooja)
                        <tr>
                            <!-- Replace Image cell (lines 98-109) -->
<td class="ps-4">
    @if($pooja->primaryMedia && $pooja->primaryMedia->file_path)
        <img src="{{ asset($pooja->primaryMedia->file_path) }}" 
             alt="{{ $pooja->name }}" 
             class="rounded object-fit-cover shadow-sm border" 
             width="50" height="50"
             onerror="this.onerror=null; this.src='https://placehold.co/50x50?text=No+Img';">
    @else
        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" 
             style="width: 50px; height: 50px;">
            <i class="bi bi-image"></i>
        </div>
    @endif
</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pooja->name }}</div>
                                <div class="text-muted small">
                                    <i class="bi bi-tag me-1"></i>{{ $pooja->category->name ?? 'Uncategorized' }}
                                </div>
                            </td>
                            <td>
                                @if($pooja->pricings->isNotEmpty())
                                    @php
                                        $firstTier = $pooja->pricings->firstWhere('is_default', true) ?? $pooja->pricings->first();
                                        $baseAmount = $firstTier ? ($firstTier->amount ?? $firstTier->price ?? 0) : 0;
                                        $discountAmount = $firstTier ? ($firstTier->discount_amount ?? 0) : 0;
                                        $finalSpecial = ($discountAmount > 0) ? ($baseAmount - $discountAmount) : null;
                                        $tierCount = $pooja->pricings->count();
                                    @endphp
                                    <div>
                                        <span class="fw-bold text-success">
                                            ₹{{ number_format($baseAmount, 2) }}
                                        </span>
                                        @if($finalSpecial)
                                            <span class="text-decoration-line-through text-muted small ms-1">
                                                ₹{{ number_format($finalSpecial, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="badge bg-light text-secondary border">
                                        {{ $tierCount }} {{ Str::plural('package', $tierCount) }}
                                    </span>
                                @else
                                    <span class="text-muted small">No pricing set</span>
                                @endif
                            </td>
                          
<td>
    @if($pooja->is_featured)
        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Popular</span>
    @else
        <span class="text-muted small">—</span>
    @endif
</td>
                            <td>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                           data-url="{{ route('admin.poojas.toggle-status', $pooja) }}"
                                           {{ $pooja->status === 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label small ms-1 status-label">
                                        {{ ucfirst($pooja->status) }}
                                    </label>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.poojas.edit', $pooja) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete('{{ $pooja->id }}', '{{ addslashes($pooja->name) }}')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $pooja->id }}" action="{{ route('admin.poojas.destroy', $pooja) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Poojas found matching your selection.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($poojas->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $poojas->links() }}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Status AJAX Switcher
    document.querySelectorAll('.status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const url = this.getAttribute('data-url');
            const label = this.closest('.form-check').querySelector('.status-label');
            const isChecked = this.checked;

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    label.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                } else {
                    this.checked = !isChecked;
                }
            })
            .catch(() => {
                this.checked = !isChecked;
                alert('Could not update status. Please try again.');
            });
        });
    });
});

function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to permanently delete "${name}"? This action removes all pricing tiers, media, and details.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection

@extends('admin.layouts.app')

@section('title', 'Manage Pooja Categories')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manage Pooja Categories</h1>
            <p class="text-muted small mb-0">
                Create, update and manage Pooja categories.
            </p>
        </div>

        <a href="{{ route('admin.pooja-categories.create') }}"
           class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Create Category</span>
        </a>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.pooja-categories.index') }}"
                  method="GET" class="row g-3">

                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control border-start-0 ps-0"
                               placeholder="Search category name..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary">
                        Search
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.pooja-categories.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-4" width="70">#</th>
                        <th>Category Name</th>
                        <th>Category Code</th>
                        <th>Slug</th>
                        <th>Associated Poojas</th>
                        <th class="text-end pe-4" width="130">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4">
                                {{ $categories->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $category->name }}
                                </div>
                                @if($category->description)
                                    <div class="text-muted small">
                                        {{ Str::limit($category->description, 70) }}
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $category->category_code }}
                                </span>
                            </td>

                            <td>
                                <span class="text-muted small">
                                    {{ $category->slug }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis">
                                    {{ $category->poojas_count ?? $category->poojas()->count() }}
                                </span>
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">

                                    <a href="{{ route('admin.pooja-categories.edit', $category) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmCategoryDelete({{ $category->id }}, {{ Js::from($category->name) }}, {{ $category->poojas_count ?? $category->poojas()->count() }})"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>

                                <form id="delete-category-{{ $category->id }}"
                                      action="{{ route('admin.pooja-categories.destroy', $category) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                                No Pooja categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if($categories->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $categories->links() }}
            </div>
        @endif

    </div>

</div>

<script>
function confirmCategoryDelete(id, name, poojaCount) {

    if (poojaCount > 0) {
        alert(
            'This category cannot be deleted because it is associated with ' +
            poojaCount + ' existing Pooja(s).'
        );
        return;
    }

    if (confirm('Are you sure you want to delete the category "' + name + '"?')) {
        document.getElementById('delete-category-' + id).submit();
    }
}
</script>
@endsection

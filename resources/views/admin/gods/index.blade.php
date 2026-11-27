
@extends('admin.layouts.app')

@section('title', 'Manage Pooja Deities')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Manage Pooja Deities</h1>
            <p class="text-muted small mb-0">
                Create, update and manage Pooja deities.
            </p>
        </div>

        <a href="{{ route('admin.gods.create') }}"
           class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
            <i class="bi bi-plus-lg"></i>
            <span>Create Deity</span>
        </a>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.gods.index') }}" method="GET" class="row g-3">

                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>

                        <input type="text"
                               name="search"
                               class="form-control border-start-0 ps-0"
                               placeholder="Search deity name..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-secondary">
                        Search
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.gods.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                            Reset
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    <!-- Deities Table -->
    <div class="card border-0 shadow-sm">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-4" width="70">#</th>
                        <th>Deity Name</th>
                        <th>Deity Code</th>
                        <th>Associated Poojas</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th class="text-end pe-4" width="130">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($gods as $god)
                        <tr>
                            <td class="ps-4">
                                {{ $gods->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $god->name }}
                                </div>

                                @if($god->short_description)
                                    <div class="text-muted small">
                                        {{ Str::limit($god->short_description, 70) }}
                                    </div>
                                @endif

                                @if($god->is_featured)
                                    <span class="badge bg-warning text-dark mt-1">
                                        <i class="bi bi-star-fill me-1"></i>Featured
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $god->god_code }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis">
                                    {{ $god->poojas_count }}
                                </span>
                            </td>

                            <td>{{ $god->sort_order }}</td>

                            <td>
                                @if($god->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">

                                    <a href="{{ route('admin.gods.edit', $god) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmGodDelete({{ $god->id }}, {{ Js::from($god->name) }}, {{ $god->poojas_count }})"
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </div>

                                <form id="delete-god-{{ $god->id }}"
                                      action="{{ route('admin.gods.destroy', $god) }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No Pooja deities found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        @if($gods->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $gods->links() }}
            </div>
        @endif

    </div>

</div>

<script>
function confirmGodDelete(id, name, poojaCount) {

    if (poojaCount > 0) {
        alert(
            'This deity cannot be deleted because it is associated with ' +
            poojaCount + ' existing Pooja(s).'
        );
        return;
    }

    if (confirm('Are you sure you want to delete the deity "' + name + '"?')) {
        document.getElementById('delete-god-' + id).submit();
    }
}
</script>
@endsection

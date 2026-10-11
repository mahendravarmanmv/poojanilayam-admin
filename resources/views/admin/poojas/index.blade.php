@extends('admin.layouts.app')
@section('title', 'Pooja Catalog')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Pooja Services Catalog</h4>
    <a href="{{ route('admin.poojas.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add New Pooja</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Associated Deities</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($poojas as $pooja)
                <tr>
                    <td class="fw-bold">{{ $pooja->name }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $pooja->category->name ?? 'Uncategorized' }}</span></td>
                    <td>
                        @foreach($pooja->gods as $god)
                            <span class="badge bg-secondary small">{{ $god->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="badge {{ $pooja->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $pooja->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.poojas.edit', $pooja) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.poojas.destroy', $pooja) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this Pooja?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No Poojas found in database.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $poojas->links() }}
    </div>
</div>
@endsection
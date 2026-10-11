@extends('admin.layouts.app')
@section('title', 'Priests & Pujaris Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Priests & Pujaris Management</h4>
    <a href="{{ route('admin.verifications.pujaris.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i> Add New Priest
    </a>
</div>

<!-- Search & Status Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.verifications.pujaris.index') }}" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by priest name, mobile, email...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">All Verification Statuses</option>
                    @foreach(['pending', 'verified', 'rejected', 'suspended'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Priest Name</th>
                    <th>Contact Phone</th>
                    <th>Email</th>
                    <th>Experience</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pujaris as $p)
                <tr>
                    <td class="fw-bold">{{ $p->user->name ?? 'Priest #'.$p->id }}</td>
                    <td>{{ $p->user->mobile ?? 'N/A' }}</td>
                    <td>{{ $p->user->email ?? 'N/A' }}</td>
                    <td>{{ $p->experience_years ?? 0 }} Years</td>
                    <td>
                        <span class="badge {{ $p->verification_status === 'verified' ? 'bg-success' : ($p->verification_status === 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                            {{ ucfirst($p->verification_status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.verifications.pujaris.show', $p) }}" class="btn btn-sm btn-outline-info me-1" title="View Dossier">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.verifications.pujaris.edit', $p) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Priest">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.verifications.pujaris.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this priest profile?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Priest">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No priests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $pujaris->links() }}
    </div>
</div>
@endsection
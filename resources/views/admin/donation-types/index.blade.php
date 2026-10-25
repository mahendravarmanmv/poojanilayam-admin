@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Donation Types</h1>
            <p class="text-muted mb-0">Manage the donation purposes shown on the public website.</p>
        </div>
        <a href="{{ route('admin.donation-types.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Donation Type
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="GET" action="{{ route('admin.donation-types.index') }}" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-7">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, code or slug">
            </div>
            <div class="col-8 col-md-4 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-4 col-md-2 col-lg-1"><button class="btn btn-primary w-100">Filter</button></div>
            <div class="col-12 col-md-2 col-lg-1"><a href="{{ route('admin.donation-types.index') }}" class="btn btn-outline-secondary w-100">Clear</a></div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Purpose</th><th>Code</th><th>Slug</th><th class="text-center">Order</th><th>Campaigns</th><th>Donations</th><th>Status</th><th class="text-end">Actions</th></tr>
                </thead>
                <tbody>
                @forelse ($donationTypes as $type)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $type->name }}</div>
                            @if ($type->description)<small class="text-muted">{{ \Illuminate\Support\Str::limit($type->description, 90) }}</small>@endif
                        </td>
                        <td><code>{{ $type->type_code }}</code></td>
                        <td>{{ $type->slug }}</td>
                        <td class="text-center">{{ $type->sort_order }}</td>
                        <td>{{ $type->campaigns_count }}</td>
                        <td>{{ $type->donations_count }}</td>
                        <td><span class="badge text-bg-{{ $type->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($type->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.donation-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.donation-types.destroy', $type) }}" class="d-inline" onsubmit="return confirm('Delete this donation type? Linked records prevent deletion.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5">
                        <div class="fw-semibold mb-1">No donation types found.</div>
                        <div class="text-muted small">Create a donation type to make it available for selection on the public donation form.</div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($donationTypes->hasPages())<div class="card-footer bg-white">{{ $donationTypes->links() }}</div>@endif
    </div>
</div>
@endsection

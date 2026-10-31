@extends('admin.layouts.app')

@section('title', 'Priest Services Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1">Priest Services Management</h4>
        <p class="text-muted mb-0">
            Manage services for {{ $pujari->user->name ?? 'Priest #'.$pujari->id }}
        </p>
    </div>

    <div>
        <a href="{{ route('admin.verifications.pujaris.index') }}"
           class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> Back to Priests
        </a>

        <a href="{{ route('admin.verifications.pujaris.services.create', $pujari->id) }}"
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Service
        </a>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-1">Priest Details</h6>
        <div class="text-muted">
            <strong>Name:</strong>
            {{ $pujari->user->name ?? 'Priest #'.$pujari->id }}
            <span class="mx-2">|</span>
            <strong>Total Services:</strong>
            {{ $services->total() }}
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th width="60">#</th>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $services->firstItem() + $loop->index }}</td>

                        <td class="fw-bold">
                            {{ $service->service_name }}
                        </td>

                        <td>
                            {{ $service->description ?: '—' }}
                        </td>

                        <td class="fw-bold">
                            ₹{{ number_format((float) $service->price, 2) }}
                        </td>

                        <td>
                            @if($service->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>

                        <td class="text-end">

                            <a href="{{ route('admin.verifications.pujaris.services.edit', [$pujari->id, $service->id]) }}"
                               class="btn btn-sm btn-outline-secondary me-1"
                               title="Edit Service">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('admin.verifications.pujaris.services.toggle-status', [$pujari->id, $service->id]) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-warning me-1"
                                        title="{{ $service->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="bi bi-power"></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.verifications.pujaris.services.destroy', [$pujari->id, $service->id]) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this service?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                        title="Delete Service">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No services added for this priest yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white py-3">
        {{ $services->links() }}
    </div>
</div>

@endsection

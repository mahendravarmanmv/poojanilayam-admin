@extends('admin.layouts.app')
@section('title', 'Temples Directory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Registered Temples</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Temple Name</th>
                    <th>Location</th>
                    <th>Verification</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($temples as $t)
                <tr>
                    <td class="fw-bold">{{ $t->name }}</td>
                    <td>{{ $t->city->name ?? 'City' }}, {{ $t->state->name ?? 'State' }}</td>
                    <td>
                        <span class="badge {{ $t->is_verified ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $t->is_verified ? 'Verified Temple' : 'Pending Verification' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.temples.show', $t) }}" class="btn btn-sm btn-outline-primary">Manage <i class="bi bi-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No temples registered.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $temples->links() }}
    </div>
</div>
@endsection
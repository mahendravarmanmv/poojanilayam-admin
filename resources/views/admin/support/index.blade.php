@extends('admin.layouts.app')
@section('title', 'Support Tickets')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Customer Support Ticket Desk</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Ticket #</th>
                    <th>Customer</th>
                    <th>Subject / Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $t)
                <tr>
                    <td class="fw-bold">#TCK-{{ $t->id }}</td>
                    <td>{{ $t->user->name ?? 'Guest User' }}</td>
                    <td>{{ $t->category->name ?? 'General Inquiry' }}</td>
                    <td><span class="badge bg-secondary">{{ $t->priority->name ?? 'Normal' }}</span></td>
                    <td><span class="badge bg-primary">{{ ucfirst($t->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.support.show', $t) }}" class="btn btn-sm btn-outline-primary">Open Ticket <i class="bi bi-arrow-right"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No active customer tickets.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('title', 'Astrology Sessions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Astrology Consultations</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Session #</th>
                    <th>Customer</th>
                    <th>Astrologer</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td class="fw-bold">{{ $b->booking_number ?? '#AST-'.$b->id }}</td>
                    <td>{{ $b->user->name ?? 'Customer' }}</td>
                    <td>{{ $b->astrologer->user->name ?? 'Assigned Astrologer' }}</td>
                    <td>{{ $b->service->name ?? 'Consultation' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($b->status) }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.astrology.show', $b) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No astrology consultations logged.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
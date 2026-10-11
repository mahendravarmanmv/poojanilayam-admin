@extends('admin.layouts.app')
@section('title', 'Digital & Live Poojas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Digital Poojas & Live Broadcast Streams</h4>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-broadcast me-2 text-danger"></i>Active Live Pooja Streams</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Pooja Name</th>
                    <th>Leading Pujari</th>
                    <th>Active Session</th>
                    <th class="text-end">Manage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($livePoojas as $lp)
                <tr>
                    <td class="fw-bold">{{ $lp->pooja->name ?? 'Live Stream Pooja' }}</td>
                    <td>{{ $lp->pujari->user->name ?? 'Temple Pujari' }}</td>
                    <td><span class="badge bg-danger">Stream Ready</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.digital-poojas.live', $lp) }}" class="btn btn-sm btn-outline-primary">Session Controls</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-3 text-muted">No live streaming sessions active.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
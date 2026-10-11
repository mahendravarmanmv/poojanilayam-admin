@extends('admin.layouts.app')
@section('title', 'Stream Controls')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.digital-poojas.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Live Streams</a>
        <h4 class="fw-bold mb-0">Stream Controls: {{ $livePooja->pooja->name ?? 'Live Stream' }}</h4>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Broadcasting Participants</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Participant</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($livePooja->participants as $part)
                            <tr>
                                <td>{{ $part->user->name ?? 'Devotee' }}</td>
                                <td>{{ $part->user->email ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-3 text-muted">No devotees joined yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Stream Session Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.digital-poojas.live.update', $livePooja) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Broadcast Status</label>
                        <select name="session_status" class="form-select" required>
                            <option value="scheduled">Scheduled</option>
                            <option value="live">Live Now</option>
                            <option value="ended">Ended</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Live Meeting URL / Stream Link</label>
                        <input type="url" name="meeting_url" class="form-control" placeholder="https://zoom.us/j/... or YouTube Live">
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Save Session Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
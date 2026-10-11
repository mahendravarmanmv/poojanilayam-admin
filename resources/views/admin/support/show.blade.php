@extends('admin.layouts.app')
@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Back to Tickets</a>
        <h4 class="fw-bold mb-0">Ticket #TCK-{{ $ticket->id }} - {{ $ticket->subject ?? 'Inquiry' }}</h4>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ ucfirst($ticket->status) }}</span>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0">Conversation Thread</h6>
    </div>
    <div class="card-body p-4">
        <div class="d-flex flex-column gap-3">
            @forelse($ticket->messages as $msg)
                <div class="p-3 rounded-3 {{ $msg->is_admin ? 'bg-primary-subtle ms-auto' : 'bg-light me-auto' }}" style="max-width: 80%;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold small {{ $msg->is_admin ? 'text-primary' : 'text-dark' }}">
                            {{ $msg->is_admin ? 'Admin Support Agent' : ($msg->user->name ?? 'Customer') }}
                        </span>
                        <span class="text-muted small ms-3">{{ $msg->created_at->format('d M, h:i A') }}</span>
                    </div>
                    <div class="text-secondary small">{{ $msg->message }}</div>
                </div>
            @empty
                <p class="text-muted small mb-0">No replies yet.</p>
            @endforelse
        </div>
    </div>
    <div class="card-footer bg-white p-3">
        <form action="{{ route('admin.support.reply', $ticket) }}" method="POST">
            @csrf
            <div class="mb-3">
                <textarea name="message" rows="3" class="form-control" placeholder="Write response to customer..." required></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <label class="small fw-semibold text-muted">Update Status:</label>
                    <select name="status" class="form-select form-select-sm" style="width: 140px;">
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-send me-1"></i> Send Reply</button>
            </div>
        </form>
    </div>
</div>
@endsection
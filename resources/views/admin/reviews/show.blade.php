
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Review Details</h1>
            <p class="text-muted mb-0">
                Review the customer's rating and feedback.
            </p>
        </div>

        <a href="{{ route('admin.reviews.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Reviews
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <strong>Review Information</strong>
        </div>

        <div class="card-body">
            @php
                $customer = $review->customerProfile?->user;
                $item = $review->reviewable;

                $statusClass = match ($review->status) {
                    'published' => 'success',
                    'rejected', 'hidden' => 'danger',
                    'pending' => 'warning',
                    default => 'secondary',
                };
            @endphp

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small">Review ID</label>
                    <div class="fw-semibold">{{ $review->review_id }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Status</label>
                    <div>
                        <span class="badge text-bg-{{ $statusClass }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Customer Name</label>
                    <div class="fw-semibold">{{ $customer?->name ?? 'Customer' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Customer Email</label>
                    <div>{{ $customer?->email ?? '—' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Reviewed Pooja / Item</label>
                    <div class="fw-semibold">
                        {{ $item?->name ?? $item?->title ?? class_basename($review->reviewable_type) }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Rating</label>
                    <div class="text-warning fs-5">
                        @for ($star = 1; $star <= 5; $star++)
                            <i class="bi {{ $star <= (int) $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                        <span class="text-dark fs-6">({{ $review->rating }}/5)</span>
                    </div>
                </div>

                <div class="col-12">
                    <label class="text-muted small">Review Title</label>
                    <div class="fw-semibold">{{ $review->title ?: 'No title provided' }}</div>
                </div>

                <div class="col-12">
                    <label class="text-muted small">Review Text</label>
                    <div class="border rounded p-3 bg-light"
                         style="white-space: pre-wrap;">{{ $review->review_text ?: 'No written feedback provided.' }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Submitted At</label>
                    <div>
                        {{ $review->submitted_at?->format('d M Y, h:i A') ?? $review->created_at?->format('d M Y, h:i A') ?? '—' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small">Published At</label>
                    <div>
                        {{ $review->published_at?->format('d M Y, h:i A') ?? 'Not published' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success message after approval or rejection --}}
    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Approval and rejection actions --}}
    @if ($review->status === 'pending')
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body d-flex flex-wrap gap-2">

                <form method="POST"
                      action="{{ route('admin.reviews.approve', $review) }}"
                      onsubmit="return confirm('Approve and publish this review?');">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Approve & Publish
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('admin.reviews.reject', $review) }}"
                      onsubmit="return confirm('Reject this review?');">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Reject
                    </button>
                </form>

            </div>
        </div>
    @endif

</div>
@endsection

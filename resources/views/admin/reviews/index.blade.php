@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Reviews & Ratings</h1>
            <p class="text-muted mb-0">
                Review customer feedback and manage review approvals for PoojaGo.
            </p>
        </div>
    </div>

    {{-- Search and status filters --}}
    <form method="GET" action="{{ route('admin.reviews.index') }}"
          class="card card-body shadow-sm border-0 mb-4">

        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-7">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    class="form-control"
                    placeholder="Search review ID, title or review text"
                >
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">All Review Statuses</option>

                    @foreach (['pending', 'published', 'rejected', 'hidden'] as $status)
                        <option value="{{ $status }}"
                            @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-3 col-lg-1">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
            </div>

            <div class="col-6 col-md-3 col-lg-1">
                <a href="{{ route('admin.reviews.index') }}"
                   class="btn btn-outline-secondary w-100">
                    Clear
                </a>
            </div>
        </div>
    </form>

    {{-- Reviews table --}}
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Review ID</th>
                        <th>Customer</th>
                        <th>Pooja / Item</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($reviews as $review)
                        @php
                            $statusClass = match ($review->status) {
                                'published' => 'success',
                                'rejected', 'hidden' => 'danger',
                                'pending' => 'warning',
                                default => 'secondary',
                            };

                            $customer = $review->customerProfile?->user;
                            $item = $review->reviewable;
                        @endphp

                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    {{ $review->review_id }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $customer?->name ?? 'Customer' }}
                                </div>
                                <small class="text-muted">
                                    {{ $customer?->email ?? 'No email' }}
                                </small>
                            </td>

                            <td>
                                {{ $item?->name ?? $item?->title ?? class_basename($review->reviewable_type) }}
                            </td>

                            <td class="text-nowrap">
                                <span class="text-warning">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <i class="bi {{ $star <= (int) $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </span>
                                <small class="text-muted">
                                    ({{ $review->rating }}/5)
                                </small>
                            </td>

                            <td style="min-width: 180px; max-width: 280px;">
                                <div class="fw-semibold">
                                    {{ $review->title ?: 'No title' }}
                                </div>
                                <small class="text-muted">
                                    {{ \Illuminate\Support\Str::limit($review->review_text, 90) ?: 'No written feedback' }}
                                </small>
                            </td>

                            <td>
                                <span class="badge text-bg-{{ $statusClass }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </td>

                            <td class="text-nowrap">
                                {{ $review->submitted_at?->format('d M Y, h:i A') ?? $review->created_at?->format('d M Y, h:i A') ?? '—' }}
                            </td>

                            <td class="text-end">
                                <a href="{{ route('admin.reviews.show', $review) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="fw-semibold mb-1">
                                    No reviews found.
                                </div>

                                <div class="text-muted small">
                                    @if (request()->filled('q') || request()->filled('status'))
                                        Try clearing the search or status filter.
                                    @else
                                        Customer reviews will appear here after submission.
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reviews->hasPages())
            <div class="card-footer bg-white">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

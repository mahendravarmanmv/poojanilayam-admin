@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Donations Directory</h1>
            <p class="text-muted mb-0">Review donation records submitted through Pooja Nilayam.</p>
        </div>

        <a href="{{ route('admin.donation-types.index') }}" class="btn btn-primary">
            <i class="bi bi-tags me-1"></i> Donation Types
        </a>
    </div>


    <form method="GET" action="{{ route('admin.donations.index') }}" class="card card-body shadow-sm border-0 mb-4">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-7">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                    placeholder="Search donation ID, reference, donor, email, phone, type or campaign">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <select name="status" class="form-select">
                    <option value="">All Donation Statuses</option>
                    @foreach (['pending','processing','successful','failed','cancelled','refunded'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 col-lg-1"><button class="btn btn-primary w-100">Filter</button></div>
            <div class="col-6 col-md-3 col-lg-1"><a href="{{ route('admin.donations.index') }}" class="btn btn-outline-secondary w-100">Clear</a></div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr>
                    <th>Donation ID</th><th>Donor</th><th>Type / Campaign</th>
                    <th class="text-end">Amount</th><th>Status</th><th>Receipt</th><th>Created Date</th><th class="text-end">Action</th>
                </tr></thead>
                <tbody>
                @forelse ($donations as $donation)
                    @php
                        $statusClass = match ($donation->status) {
                            'successful' => 'success',
                            'failed', 'cancelled', 'refunded' => 'danger',
                            'processing' => 'info',
                            default => 'warning',
                        };
                    @endphp
                    <tr>
                        <td><div class="fw-semibold">{{ $donation->donation_id }}</div><small class="text-muted">{{ $donation->reference_id }}</small></td>
                        <td>
                            <div class="fw-semibold">{{ $donation->is_anonymous ? 'Anonymous donor' : $donation->donor_name }}</div>
                            @unless ($donation->is_anonymous)
                                <small class="text-muted d-block">{{ $donation->donor_email ?: 'No email' }}</small>
                                <small class="text-muted">{{ $donation->donor_mobile ?: 'No phone' }}</small>
                            @endunless
                        </td>
                        <td><div>{{ $donation->donationType?->name ?? '—' }}</div><small class="text-muted">{{ $donation->donationCampaign?->name ?? ($donation->temple?->name ?? 'General donation') }}</small></td>
                        <td class="text-end text-nowrap">{{ $donation->currency_code }} {{ number_format((float) $donation->amount, 2) }}</td>
                        <td><span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($donation->status) }}</span></td>
                        <td>
                            @if ($donation->receipt)
                                <span class="badge text-bg-{{ $donation->receipt->status === 'issued' ? 'success' : 'secondary' }}">{{ ucfirst($donation->receipt->status) }}</span>
                                <small class="d-block text-muted">{{ $donation->receipt->receipt_number }}</small>
                            @else <span class="text-muted">Not issued</span> @endif
                        </td>
                        <td class="text-nowrap">{{ $donation->created_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td class="text-end"><a href="{{ route('admin.donations.show', $donation) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5">
                        <div class="fw-semibold mb-1">No donations found.</div>
                        <div class="text-muted small">
                            @if (request()->filled('q') || request()->filled('status'))
                                Try clearing the search or status filter.
                            @else
                                Donation records will appear here after the frontend submission flow saves them.
                            @endif
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if ($donations->hasPages())<div class="card-footer bg-white">{{ $donations->links() }}</div>@endif
    </div>
</div>
@endsection

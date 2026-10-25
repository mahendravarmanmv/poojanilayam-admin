@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.donations.index') }}" class="text-decoration-none small">&larr; Back to donations</a>
            <h1 class="h3 mt-2 mb-1">Donation Details</h1>
            <p class="text-muted mb-0">{{ $donation->donation_id }}</p>
        </div>
        @php
            $statusClass = match ($donation->status) {
                'successful' => 'success',
                'failed', 'cancelled', 'refunded' => 'danger',
                'processing' => 'info',
                default => 'warning',
            };
        @endphp
        <span class="badge text-bg-{{ $statusClass }} fs-6">{{ ucfirst($donation->status) }}</span>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold">Donation Information</div>
                <div class="card-body"><dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Donation ID</dt><dd class="col-sm-8">{{ $donation->donation_id }}</dd>
                    <dt class="col-sm-4 text-muted">Reference ID</dt><dd class="col-sm-8">{{ $donation->reference_id }}</dd>
                    <dt class="col-sm-4 text-muted">Amount</dt><dd class="col-sm-8 fw-semibold">{{ $donation->currency_code }} {{ number_format((float) $donation->amount, 2) }}</dd>
                    <dt class="col-sm-4 text-muted">Donation Type</dt><dd class="col-sm-8">{{ $donation->donationType?->name ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Campaign</dt><dd class="col-sm-8">{{ $donation->donationCampaign?->name ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Temple</dt><dd class="col-sm-8">{{ $donation->temple?->name ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Purpose</dt><dd class="col-sm-8">{{ $donation->purpose ?: '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Donor Message</dt><dd class="col-sm-8">{{ $donation->donor_message ?: '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Payment Reference</dt><dd class="col-sm-8">{{ $donation->payment_reference ?: '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Paid At</dt><dd class="col-sm-8">{{ $donation->paid_at?->format('d M Y, h:i A') ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Created At</dt><dd class="col-sm-8">{{ $donation->created_at?->format('d M Y, h:i A') ?? '—' }}</dd>
                </dl></div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Status History</div>
                <div class="table-responsive"><table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>From</th><th>To</th><th>Source</th><th>Changed By</th><th>Changed At</th><th>Remarks</th></tr></thead>
                    <tbody>
                    @forelse ($donation->statusHistories->sortByDesc('changed_at') as $history)
                        <tr>
                            <td>{{ $history->from_status ? ucfirst($history->from_status) : '—' }}</td>
                            <td>{{ ucfirst($history->to_status) }}</td><td>{{ $history->source ?: '—' }}</td>
                            <td>{{ $history->changedBy?->name ?? 'System' }}</td>
                            <td class="text-nowrap">{{ $history->changed_at?->format('d M Y, h:i A') ?? '—' }}</td>
                            <td>{{ $history->remarks ?: '—' }}</td>
                        </tr>
                    @empty <tr><td colspan="6" class="text-center text-muted py-4">No status history recorded.</td></tr> @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>

        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-semibold">Donor Details</div>
                <div class="card-body">
                    @if ($donation->is_anonymous)
                        <p class="mb-0">This donor chose to remain anonymous.</p>
                    @else
                        <h2 class="h5">{{ $donation->donor_name }}</h2>
                        <p class="mb-2">{{ $donation->donor_email ?: 'No email provided' }}</p>
                        <p class="mb-0">{{ $donation->donor_mobile ?: 'No phone provided' }}</p>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">Receipt</div>
                <div class="card-body">
                    @if ($donation->receipt)
                        <dl class="row mb-0">
                            <dt class="col-5 text-muted">Receipt No.</dt><dd class="col-7">{{ $donation->receipt->receipt_number }}</dd>
                            <dt class="col-5 text-muted">Status</dt><dd class="col-7">{{ ucfirst($donation->receipt->status) }}</dd>
                            <dt class="col-5 text-muted">Issued At</dt><dd class="col-7">{{ $donation->receipt->issued_at?->format('d M Y, h:i A') ?? '—' }}</dd>
                        </dl>
                        @if ($donation->receipt->file_url)
                            <a class="btn btn-outline-primary w-100 mt-3" href="{{ $donation->receipt->file_url }}" target="_blank" rel="noopener">Open Receipt</a>
                        @elseif ($donation->receipt->file_path)
                            <p class="small text-muted mt-3 mb-0">Receipt file path: {{ $donation->receipt->file_path }}</p>
                        @endif
                    @else
                        <p class="text-muted mb-0">No receipt has been issued for this donation.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')
@section('title', 'Finance & Payouts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Settlements & Pujari / Vendor Payouts</h4>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Payout Requests</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Payout #</th>
                    <th>Payee</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Processed At</th>
                    <th class="text-end">Execution</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td class="fw-bold">#PAY-{{ $p->id }}</td>
                    <td>{{ $p->user->name ?? 'Vendor / Pujari' }}</td>
                    <td class="fw-bold text-success">₹{{ number_format($p->amount, 2) }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($p->status) }}</span></td>
                    <td>{{ $p->processed_at ? $p->processed_at->format('d M Y') : 'Pending Bank Transfer' }}</td>
                    <td class="text-end">
                        @if($p->status !== 'completed')
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payoutModal{{ $p->id }}">
                            Execute Payout
                        </button>

                        <!-- Payout Modal -->
                        <div class="modal fade" id="payoutModal{{ $p->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content text-start">
                                    <form action="{{ route('admin.settlements.payout', $p) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Authorize Payout #PAY-{{ $p->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Bank Reference / UTR Number</label>
                                                <input type="text" name="transaction_reference" class="form-control" required placeholder="E.g., UTR9876543210">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Status Result</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="completed">Completed / Settled</option>
                                                    <option value="failed">Failed Transfer</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Audit Notes</label>
                                                <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success">Confirm Execution</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                        <span class="text-success small fw-bold"><i class="bi bi-check-circle-fill"></i> Settled</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No pending payout requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $payouts->links() }}
    </div>
</div>
@endsection
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Models\Payout;
use App\Models\PayoutStatusHistory;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSettlementController extends Controller
{
    public function index(Request $request)
    {
        $settlements = Settlement::with(['user', 'items'])->latest()->paginate(15);
        $payouts = Payout::with(['user', 'statusHistories'])->latest()->paginate(15);

        return view('admin.finances.index', compact('settlements', 'payouts'));
    }

    public function executePayout(Request $request, Payout $payout)
    {
        $validated = $request->validate([
            'transaction_reference' => 'required|string|max:100',
            'status'                => 'required|in:completed,failed',
            'notes'                 => 'nullable|string',
        ]);

        DB::transaction(function () use ($payout, $validated) {
            $oldStatus = $payout->status;
            $payout->update([
                'status'                => $validated['status'],
                'transaction_reference' => $validated['transaction_reference'],
                'processed_at'          => now(),
            ]);

            PayoutStatusHistory::create([
                'payout_id'   => $payout->id,
                'from_status' => $oldStatus,
                'to_status'   => $validated['status'],
                'changed_by'  => auth()->id(),
                'notes'       => $validated['notes'] ?? 'Processed via Admin Banking Execution',
            ]);
        });

        return back()->with('success', 'Payout execution finalized.');
    }
}
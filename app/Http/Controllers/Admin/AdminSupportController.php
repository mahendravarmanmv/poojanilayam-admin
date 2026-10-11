<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\SupportTicketStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'category', 'priority']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();

        return view('admin.support.index', compact('tickets'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['user', 'category', 'priority', 'messages.user', 'attachments']);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'status'  => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'user_id'           => auth()->id(),
                'message'           => $validated['message'],
                'is_admin'          => true,
            ]);

            if (!empty($validated['status']) && $validated['status'] !== $ticket->status) {
                $oldStatus = $ticket->status;
                $ticket->update(['status' => $validated['status']]);

                SupportTicketStatusHistory::create([
                    'support_ticket_id' => $ticket->id,
                    'from_status'       => $oldStatus,
                    'to_status'         => $validated['status'],
                    'changed_by'        => auth()->id(),
                    'change_reason'     => 'Status updated along with admin reply',
                ]);
            }
        });

        return redirect()->route('admin.support.show', $ticket)->with('success', 'Response recorded.');
    }
}
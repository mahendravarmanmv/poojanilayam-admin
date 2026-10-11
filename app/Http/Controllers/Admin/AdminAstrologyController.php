<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologyBooking;
use App\Models\AstrologerProfile;
use App\Models\AstrologyBookingStatusHistory;
use App\Models\AstrologyConsultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAstrologyController extends Controller
{
    public function index(Request $request)
    {
        $query = AstrologyBooking::with(['user', 'astrologer.user', 'service']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.astrology.index', compact('bookings'));
    }

    public function show(AstrologyBooking $booking)
    {
        $booking->load(['user', 'astrologer.user', 'service', 'consultation.messages', 'reports']);
        return view('admin.astrology.show', compact('booking'));
    }

    public function updateStatus(Request $request, AstrologyBooking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,scheduled,in_progress,completed,cancelled',
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $oldStatus = $booking->status;
            $booking->update(['status' => $validated['status']]);

            AstrologyBookingStatusHistory::create([
                'astrology_booking_id' => $booking->id,
                'from_status'          => $oldStatus,
                'to_status'            => $validated['status'],
                'changed_by'           => auth()->id(),
                'notes'                => $validated['reason'],
            ]);
        });

        return redirect()->route('admin.astrology.show', $booking)->with('success', 'Astrology booking status updated.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAssignment;
use App\Models\BookingStatusHistory;
use App\Models\PujariProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'pooja', 'pujari']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('booking_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'user', 
            'pooja.details', 
            'pujari.user', 
            'address', 
            'sankalpam.members', 
            'extras', 
            'deliverables', 
            'statusHistories.changedBy'
        ]);

        $availablePujaris = PujariProfile::with('user')->where('verification_status', 'verified')->get();

        return view('admin.bookings.show', compact('booking', 'availablePujaris'));
    }

    public function assignPujari(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'pujari_id' => 'required|exists:pujari_profiles,id',
            'notes'     => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            BookingAssignment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'pujari_id'   => $validated['pujari_id'],
                    'assigned_by' => auth()->id(),
                    'assigned_at' => now(),
                    'status'      => 'assigned',
                    'notes'       => $validated['notes'] ?? null,
                ]
            );

            $oldStatus = $booking->status;
            $booking->update([
                'status'    => 'assigned',
                'pujari_id' => $validated['pujari_id'],
            ]);

            BookingStatusHistory::create([
                'booking_id'    => $booking->id,
                'from_status'   => $oldStatus,
                'to_status'     => 'assigned',
                'changed_by'    => auth()->id(),
                'change_reason' => 'Pujari assigned via Admin Portal: ' . ($validated['notes'] ?? 'None'),
            ]);
        });

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Pujari assigned successfully.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,assigned,in_progress,completed,cancelled',
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $oldStatus = $booking->status;
            $booking->update(['status' => $validated['status']]);

            BookingStatusHistory::create([
                'booking_id'    => $booking->id,
                'from_status'   => $oldStatus,
                'to_status'     => $validated['status'],
                'changed_by'    => auth()->id(),
                'change_reason' => $validated['reason'],
            ]);
        });

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking status updated.');
    }
}
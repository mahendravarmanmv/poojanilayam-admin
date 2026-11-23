<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriestBooking;
use Illuminate\Http\Request;

class AdminPriestBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = PriestBooking::with([
            'payments'
        ]);

        // Filter by booking status
        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->booking_status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        $bookings = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.priest-bookings.index', compact('bookings'));
    }

    public function show(PriestBooking $priestBooking)
    {
        $priestBooking->load('payments');

        return view(
            'admin.priest-bookings.show',
            compact('priestBooking')
        );
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\PujariProfile;
use App\Models\AstrologyBooking;
use App\Models\Donation;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings'      => Booking::count(),
            'pending_bookings'    => Booking::where('status', 'pending')->count(),
            'total_orders'        => Order::count(),
            'processing_orders'   => Order::where('status', 'processing')->count(),
            'total_donations'     => Donation::where('status', 'completed')->sum('amount'),
            'pending_pujaris'     => PujariProfile::where('verification_status', 'pending')->count(),
            'astrology_bookings'  => AstrologyBooking::where('status', 'scheduled')->count(),
            'open_tickets'        => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
        ];

        $recentBookings = Booking::with(['user', 'pooja'])->latest()->limit(5)->get();
        $recentOrders = Order::with('customerProfile')->latest()->limit(5)->get();
        $pendingTickets = SupportTicket::with('user')->where('status', 'open')->latest()->limit(5)->get();

        return view('admin.dashboard.index', compact('stats', 'recentBookings', 'recentOrders', 'pendingTickets'));
    }
}
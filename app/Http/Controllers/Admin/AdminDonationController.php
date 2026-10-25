<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDonationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:191'],
            'status' => ['nullable', 'in:pending,processing,successful,failed,cancelled,refunded'],
        ]);

        $query = Donation::query()->with([
            'donationType:id,name',
            'donationCampaign:id,name',
            'temple:id,name',
            'receipt:id,donation_id,receipt_number,status,issued_at',
        ])->latest('created_at');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(function (Builder $q) use ($term) {
                $q->where('donation_id', 'like', "%{$term}%")
                  ->orWhere('reference_id', 'like', "%{$term}%")
                  ->orWhere('donor_name', 'like', "%{$term}%")
                  ->orWhere('donor_email', 'like', "%{$term}%")
                  ->orWhere('donor_mobile', 'like', "%{$term}%")
                  ->orWhereHas('donationType', fn (Builder $r) => $r->where('name', 'like', "%{$term}%"))
                  ->orWhereHas('donationCampaign', fn (Builder $r) => $r->where('name', 'like', "%{$term}%"));
            });
        }

        $donations = $query->paginate(15)->withQueryString();
        return view('admin.donations.index', compact('donations'));
    }

    public function show(Donation $donation): View
    {
        $donation->load([
            'donationType', 'donationCampaign', 'temple', 'currency',
            'receipt', 'statusHistories.changedBy',
        ]);

        return view('admin.donations.show', compact('donation'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Temple;
use App\Models\TempleProfile;
use App\Models\TempleVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminTempleController extends Controller
{
    public function index(Request $request)
    {
        $query = Temple::with(['profile', 'city', 'state']);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $temples = $query->latest()->paginate(15)->withQueryString();

        return view('admin.temples.index', compact('temples'));
    }

    public function show(Temple $temple)
    {
        $temple->load(['profile', 'timings', 'events', 'poojas', 'pujaris.user', 'galleries']);
        return view('admin.temples.show', compact('temple'));
    }

    public function verify(Request $request, Temple $temple)
    {
        $validated = $request->validate([
            'status'  => 'required|in:verified,rejected,pending',
            'remarks' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($temple, $validated) {
            $temple->update(['is_verified' => ($validated['status'] === 'verified')]);

            TempleVerification::create([
                'temple_id'   => $temple->id,
                'verified_by' => auth()->id(),
                'status'      => $validated['status'],
                'remarks'     => $validated['remarks'],
                'verified_at' => now(),
            ]);
        });

        return redirect()->route('admin.temples.show', $temple)->with('success', 'Temple verification status updated.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalPooja;
use App\Models\LivePooja;
use App\Models\LivePoojaSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDigitalPoojaController extends Controller
{
    public function index(Request $request)
    {
        $digitalPoojas = DigitalPooja::with(['template', 'user'])->latest()->paginate(15);
        $livePoojas = LivePooja::with(['pooja', 'pujari.user'])->latest()->paginate(15);

        return view('admin.digital-poojas.index', compact('digitalPoojas', 'livePoojas'));
    }

    public function showLivePooja(LivePooja $livePooja)
    {
        $livePooja->load(['pooja', 'pujari.user', 'sessions', 'participants.user']);
        return view('admin.digital-poojas.show-live', compact('livePooja'));
    }

    public function updateSession(Request $request, LivePooja $livePooja)
    {
        $validated = $request->validate([
            'session_status' => 'required|in:scheduled,live,ended,cancelled',
            'meeting_url'    => 'nullable|url',
        ]);

        $livePooja->sessions()->updateOrCreate(
            ['live_pooja_id' => $livePooja->id],
            [
                'status'      => $validated['session_status'],
                'meeting_url' => $validated['meeting_url'] ?? null,
            ]
        );

        return back()->with('success', 'Live session updated.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PujariProfile;
use App\Models\PujariAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AdminPujariAvailabilityController extends Controller
{
	/**
 * Display priest availability management page.
 */
	public function index(PujariProfile $pujari)
	{
		$availabilities = PujariAvailability::where('pujari_profile_id', $pujari->id)
			->orderByRaw("CASE WHEN availability_type = 'specific' THEN 0 ELSE 1 END")
			->orderBy('availability_date')
			->orderBy('day_of_week')
			->orderBy('start_time')
			->get();

		return view('admin.verifications.pujaris.availability.index', compact(
			'pujari',
			'availabilities'
		));
	}
    /**
     * Store priest availability.
     */
    public function store(Request $request, PujariProfile $pujari): RedirectResponse
    {
        $validated = $this->validateAvailability($request);

        $validated['pujari_profile_id'] = $pujari->id;

        PujariAvailability::create($validated);

        return back()->with('success', 'Priest availability added successfully.');
    }

    /**
     * Update priest availability.
     */
    public function update(
        Request $request,
        PujariProfile $pujari,
        $availability
    ): RedirectResponse {
        $validated = $this->validateAvailability($request);

        $record = PujariAvailability::where('pujari_profile_id', $pujari->id)
            ->findOrFail($availability);

        $record->update($validated);

        return back()->with('success', 'Priest availability updated successfully.');
    }

    /**
     * Delete priest availability.
     */
    public function destroy(
        PujariProfile $pujari,
        $availability
    ): RedirectResponse {
        $record = PujariAvailability::where('pujari_profile_id', $pujari->id)
            ->findOrFail($availability);

        $record->delete();

        return back()->with('success', 'Priest availability deleted successfully.');
    }

    /**
     * Validate availability details.
     */
    private function validateAvailability(Request $request): array
    {
        $validated = $request->validate([
            'availability_type' => 'required|in:specific,recurring',

            'availability_date' => [
                'nullable',
                'required_if:availability_type,specific',
                'date',
                'after_or_equal:today',
            ],

            'day_of_week' => [
                'nullable',
                'required_if:availability_type,recurring',
                'integer',
                'between:0,6',
            ],

            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',

            'status' => 'required|in:available,unavailable',
            'timezone' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        // Keep specific-date and recurring fields separate.
        if ($validated['availability_type'] === 'specific') {
            $validated['day_of_week'] = null;
        } else {
            $validated['availability_date'] = null;
        }

        return $validated;
    }
}

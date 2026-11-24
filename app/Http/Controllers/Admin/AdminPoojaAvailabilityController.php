<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\TemplePoojaSchedule;
use App\Http\Controllers\Controller;
use App\Models\Pooja;

class AdminPoojaAvailabilityController extends Controller
{
    public function index()
    {
        $poojas = Pooja::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.pooja-availability.index', compact('poojas'));
    }
	
	public function store(Request $request)
	{
    $rules = [
        'pooja_id' => ['required', 'exists:poojas,id'],
        'schedule_type' => ['required', 'in:recurring,specific_date'],
        'start_time' => ['required', 'date_format:H:i'],
        'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        'slot_duration_minutes' => ['required', 'integer', 'min:1'],
        'capacity' => ['required', 'integer', 'min:1'],
        'notes' => ['nullable', 'string', 'max:1000'],
    ];

    if ($request->schedule_type === 'recurring') {
        $rules['day_of_week'] = ['required', 'integer', 'between:0,6'];
    } else {
        $rules['schedule_date'] = ['required', 'date', 'after_or_equal:today'];
    }

    $validated = $request->validate($rules);

    TemplePoojaSchedule::create([
        'pooja_id' => $validated['pooja_id'],
        'temple_pooja_id' => null,
        'schedule_type' => $validated['schedule_type'],
        'day_of_week' => $validated['day_of_week'] ?? null,
        'schedule_date' => $validated['schedule_date'] ?? null,
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'slot_duration_minutes' => $validated['slot_duration_minutes'],
        'capacity' => $validated['capacity'],
        'status' => 'active',
        'timezone' => 'Asia/Kolkata',
        'notes' => $validated['notes'] ?? null,
    ]);

    return redirect()
        ->route('admin.pooja-availability.index')
        ->with('success', 'Availability schedule added successfully.');
	}
}
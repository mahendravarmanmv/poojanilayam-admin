<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookingSlot;
use App\Http\Controllers\Controller;
use App\Models\Pooja;
use App\Models\TemplePoojaSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminPoojaAvailabilityController extends Controller
{
    private string $timezone = 'Asia/Kolkata';

    public function index(Request $request)
    {
        $poojas = Pooja::query()
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $schedules = TemplePoojaSchedule::query()
            ->with('pooja')
            ->withCount('bookingSlots')
            ->when($request->filled('pooja_id'), function ($query) use ($request) {
                $query->where('pooja_id', $request->pooja_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
			
			$slotSummaryQuery = DB::table('booking_slots as bs')
			->join(
			'temple_pooja_schedules as tps',
			'tps.id',
			'=',
			'bs.temple_pooja_schedule_id'
			)
			->whereNotNull('tps.pooja_id')
			->whereNull('tps.deleted_at')
			->where('tps.status', 'active');

			$activeSchedulesCount = TemplePoojaSchedule::query()
			->whereNotNull('pooja_id')
			->where('status', 'active')
			->count();

			$slotSummary = [
			'total' => (clone $slotSummaryQuery)->count(),

			'available' => (clone $slotSummaryQuery)
			->where('bs.status', 'available')
			->whereRaw('bs.booked_count < bs.capacity')
			->count(),

			'fully_booked' => (clone $slotSummaryQuery)
			->where('bs.status', 'available')
			->whereRaw('bs.booked_count >= bs.capacity')
			->count(),

			'blocked' => (clone $slotSummaryQuery)
			->where('bs.status', 'blocked')
			->count(),
			];

			return view('admin.pooja-availability.index', compact(
			'poojas',
			'schedules',
			'activeSchedulesCount',
			'slotSummary'
			));
    }

    public function store(Request $request)
    {
        $rules = [
            'pooja_id' => ['required', 'exists:poojas,id'],
            'schedule_type' => [
                'required',
                'in:recurring,specific_date,date_range,multiple_dates'
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time'
            ],
            'slot_duration_minutes' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($request->schedule_type === 'recurring') {
            $rules['day_of_week'] = [
                'required', 'integer', 'between:0,6'
            ];
        } elseif ($request->schedule_type === 'specific_date') {
            $rules['schedule_date'] = [
                'required', 'date', 'after_or_equal:today'
            ];
        } elseif ($request->schedule_type === 'date_range') {
            $rules['range_start_date'] = [
                'required', 'date', 'after_or_equal:today'
            ];
            $rules['range_end_date'] = [
                'required', 'date', 'after_or_equal:range_start_date'
            ];
        } elseif ($request->schedule_type === 'multiple_dates') {
            $rules['schedule_dates'] = [
                'required', 'array', 'min:1'
            ];
            $rules['schedule_dates.*'] = [
                'required', 'date', 'after_or_equal:today', 'distinct'
            ];
        }

        $validated = $request->validate($rules);

        $dates = $this->resolveDates($validated);

        $createdSlots = 0;

        DB::transaction(function () use (
            $validated,
            $dates,
            &$createdSlots
        ) {
            if ($validated['schedule_type'] === 'recurring') {
                $this->assertNoRecurringOverlap(
                    (int) $validated['pooja_id'],
                    (int) $validated['day_of_week'],
                    $validated['start_time'],
                    $validated['end_time']
                );

                $schedule = $this->createSchedule(
                    $validated,
                    'recurring',
                    null,
                    (int) $validated['day_of_week']
                );

                $createdSlots += $this->generateSlots(
                    $schedule,
                    $this->nextRecurringDates((int) $validated['day_of_week'])
                );

                return;
            }

            foreach ($dates as $date) {
                $this->assertNoDateOverlap(
                    (int) $validated['pooja_id'],
                    $date,
                    $validated['start_time'],
                    $validated['end_time']
                );

                // All date-based schedules are stored as "specific"
                // to remain compatible with the existing availability service.
                $schedule = $this->createSchedule(
                    $validated,
                    'specific',
                    $date,
                    null
                );

                $createdSlots += $this->generateSlots($schedule, [$date]);
            }
        });

        return redirect()
            ->route('admin.pooja-availability.index')
            ->with(
                'success',
                "Availability saved successfully. {$createdSlots} slots generated."
            );
    }

    public function edit(TemplePoojaSchedule $schedule)
    {
        $schedule->load('pooja');

        return view('admin.pooja-availability.edit', compact('schedule'));
    }

    public function update(Request $request, TemplePoojaSchedule $schedule)
    {
        $rules = [
            'schedule_type' => ['required', 'in:recurring,specific'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required', 'date_format:H:i', 'after:start_time'
            ],
            'slot_duration_minutes' => ['required', 'integer', 'min:1'],
            'capacity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($request->schedule_type === 'recurring') {
            $rules['day_of_week'] = [
                'required', 'integer', 'between:0,6'
            ];
        } else {
            $rules['schedule_date'] = [
                'required', 'date', 'after_or_equal:today'
            ];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $schedule) {
            $this->assertScheduleCanBeRebuilt($schedule);

            if ($validated['schedule_type'] === 'recurring') {
                $this->assertNoRecurringOverlap(
                    (int) $schedule->pooja_id,
                    (int) $validated['day_of_week'],
                    $validated['start_time'],
                    $validated['end_time'],
                    $schedule->id
                );

                $scheduleDate = null;
                $dayOfWeek = (int) $validated['day_of_week'];
            } else {
                $scheduleDate = $validated['schedule_date'];
                $dayOfWeek = null;

                $this->assertNoDateOverlap(
                    (int) $schedule->pooja_id,
                    $scheduleDate,
                    $validated['start_time'],
                    $validated['end_time'],
                    $schedule->id
                );
            }

            // Existing slots are removed only after confirming
            // there are no bookings or lock records for this schedule.
            $schedule->bookingSlots()->delete();

            $schedule->update([
                'schedule_type' => $validated['schedule_type'],
                'day_of_week' => $dayOfWeek,
                'schedule_date' => $scheduleDate,
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'slot_duration_minutes' => $validated['slot_duration_minutes'],
                'capacity' => $validated['capacity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $dates = $validated['schedule_type'] === 'recurring'
                ? $this->nextRecurringDates($dayOfWeek)
                : [$scheduleDate];

            $this->generateSlots($schedule, $dates);
        });

        return redirect()
            ->route('admin.pooja-availability.index')
            ->with('success', 'Availability schedule updated successfully.');
    }

    public function toggleStatus(TemplePoojaSchedule $schedule)
    {
        if ($schedule->status === 'active') {
            $schedule->update(['status' => 'inactive']);

            return back()->with('success', 'Schedule deactivated successfully.');
        }

        if ($schedule->schedule_type === 'recurring') {
            $this->assertNoRecurringOverlap(
                (int) $schedule->pooja_id,
                (int) $schedule->day_of_week,
                $schedule->start_time,
                $schedule->end_time,
                $schedule->id
            );
        } else {
            $this->assertNoDateOverlap(
                (int) $schedule->pooja_id,
                $schedule->schedule_date->toDateString(),
                $schedule->start_time,
                $schedule->end_time,
                $schedule->id
            );
        }

        $schedule->update(['status' => 'active']);

        return back()->with('success', 'Schedule activated successfully.');
    }

    public function destroy(TemplePoojaSchedule $schedule)
    {
        // Soft delete only. Generated slots, bookings, locks and
        // reschedule references are deliberately retained.
        $schedule->delete();

        return redirect()
            ->route('admin.pooja-availability.index')
            ->with('success', 'Schedule removed from availability.');
    }

    private function resolveDates(array $validated): array
    {
        if ($validated['schedule_type'] === 'specific_date') {
            return [$validated['schedule_date']];
        }

        if ($validated['schedule_type'] === 'multiple_dates') {
            $dates = array_values(array_unique($validated['schedule_dates']));
            sort($dates);

            return $dates;
        }

        if ($validated['schedule_type'] === 'date_range') {
            $dates = [];

            foreach (CarbonPeriod::create(
                $validated['range_start_date'],
                $validated['range_end_date']
            ) as $date) {
                $dates[] = $date->toDateString();
            }

            return $dates;
        }

        return [];
    }

    private function createSchedule(
        array $data,
        string $type,
        ?string $date,
        ?int $dayOfWeek
    ): TemplePoojaSchedule {
        return TemplePoojaSchedule::create([
            'pooja_id' => $data['pooja_id'],
            'temple_pooja_id' => null,
            'schedule_type' => $type,
            'day_of_week' => $dayOfWeek,
            'schedule_date' => $date,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'slot_duration_minutes' => $data['slot_duration_minutes'],
            'capacity' => $data['capacity'],
            'status' => 'active',
            'timezone' => $this->timezone,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    private function nextRecurringDates(int $dayOfWeek): array
    {
        $dates = [];
        $start = Carbon::today($this->timezone);
        $end = $start->copy()->addDays(89);

        foreach (CarbonPeriod::create($start, $end) as $date) {
            if ($date->dayOfWeek === $dayOfWeek) {
                $dates[] = $date->toDateString();
            }
        }

        return $dates;
    }

    private function generateSlots(
        TemplePoojaSchedule $schedule,
        array $dates
    ): int {
        $inserted = 0;
        $duration = (int) $schedule->slot_duration_minutes;

        foreach ($dates as $date) {
            $start = Carbon::parse(
                $date . ' ' . $schedule->start_time,
                $this->timezone
            );

            $end = Carbon::parse(
                $date . ' ' . $schedule->end_time,
                $this->timezone
            );

            $rows = [];

            while ($start->copy()->addMinutes($duration)->lessThanOrEqualTo($end)) {
                $slotEnd = $start->copy()->addMinutes($duration);

                $rows[] = [
                    'temple_pooja_schedule_id' => $schedule->id,
                    'slot_date' => $date,
                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                    'capacity' => $schedule->capacity,
                    'booked_count' => 0,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $start = $slotEnd;
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                $inserted += DB::table('booking_slots')->insertOrIgnore($chunk);
            }
        }

        return $inserted;
    }

    private function assertNoRecurringOverlap(
        int $poojaId,
        int $dayOfWeek,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): void {
        $query = TemplePoojaSchedule::query()
            ->where('pooja_id', $poojaId)
            ->where('status', 'active')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->where(function ($q) use ($dayOfWeek) {
                $q->where(function ($r) use ($dayOfWeek) {
                    $r->where('schedule_type', 'recurring')
                        ->where('day_of_week', $dayOfWeek);
                })->orWhere(function ($r) use ($dayOfWeek) {
                    $r->where('schedule_type', 'specific')
                        ->whereRaw(
                            'DAYOFWEEK(schedule_date) = ?',
                            [$dayOfWeek + 1]
                        )
                        ->whereDate('schedule_date', '>=', Carbon::today($this->timezone));
                });
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'schedule_type' =>
                    'An overlapping active schedule already exists for this Pooja and weekday.'
            ]);
        }
    }

    private function assertNoDateOverlap(
        int $poojaId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreId = null
    ): void {
        $dayOfWeek = Carbon::parse($date, $this->timezone)->dayOfWeek;

        $query = TemplePoojaSchedule::query()
            ->where('pooja_id', $poojaId)
            ->where('status', 'active')
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->where(function ($q) use ($date, $dayOfWeek) {
                $q->where(function ($r) use ($date) {
                    $r->where('schedule_type', 'specific')
                        ->whereDate('schedule_date', $date);
                })->orWhere(function ($r) use ($dayOfWeek) {
                    $r->where('schedule_type', 'recurring')
                        ->where('day_of_week', $dayOfWeek);
                });
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'schedule_date' =>
                    "An overlapping availability already exists for {$date}."
            ]);
        }
    }

    private function assertScheduleCanBeRebuilt(
        TemplePoojaSchedule $schedule
    ): void {
        $slotIds = DB::table('booking_slots')
            ->select('id')
            ->where('temple_pooja_schedule_id', $schedule->id);

        $hasBookings = DB::table('bookings')
            ->whereIn('booking_slot_id', $slotIds)
            ->exists();

        $hasLocks = DB::table('booking_slot_locks')
            ->whereIn('booking_slot_id', $slotIds)
            ->exists();

        if ($hasBookings || $hasLocks) {
            throw ValidationException::withMessages([
                'schedule' =>
                    'This schedule cannot be edited because its slots have booking or lock history. Deactivate it and create a new schedule instead.'
            ]);
        }
    }
	
	
	public function slots(Request $request, TemplePoojaSchedule $schedule)
	{
		$request->validate([
			'date' => ['nullable', 'date'],
			'status' => ['nullable', 'in:available,blocked'],
		]);

		$schedule->load('pooja');

		$slots = BookingSlot::query()
			->where('temple_pooja_schedule_id', $schedule->id)
			->withCount('bookings')
			->withCount([
				'locks as active_locks_count' => function ($query) {
					$query->whereNull('released_at')
						->where(function ($q) {
							$q->whereNull('expires_at')
								->orWhere('expires_at', '>', now());
						});
				},
			])
			->when($request->filled('date'), function ($query) use ($request) {
				$query->whereDate('slot_date', $request->date);
			})
			->when($request->filled('status'), function ($query) use ($request) {
				$query->where('status', $request->status);
			})
			->orderBy('slot_date')
			->orderBy('start_time')
			->paginate(30)
			->withQueryString();

		return view('admin.pooja-availability.slots', compact(
			'schedule',
			'slots'
		));
	}
	
	public function blockSlot(
    TemplePoojaSchedule $schedule,
    BookingSlot $slot
) {
    DB::transaction(function () use ($schedule, $slot) {
        $slot = BookingSlot::query()
            ->where('id', $slot->id)
            ->where('temple_pooja_schedule_id', $schedule->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($schedule->status !== 'active') {
            throw ValidationException::withMessages([
                'slot' => 'Slots can only be blocked under an active schedule.'
            ]);
        }

        if ($slot->status === 'blocked') {
            return;
        }

        $hasBookings = DB::table('bookings')
            ->where('booking_slot_id', $slot->id)
            ->exists();

        $hasActiveLocks = DB::table('booking_slot_locks')
            ->where('booking_slot_id', $slot->id)
            ->whereNull('released_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($slot->booked_count > 0 || $hasBookings || $hasActiveLocks) {
            throw ValidationException::withMessages([
                'slot' => 'This slot cannot be blocked because it has bookings or active locks.'
            ]);
        }

        $slot->update([
            'status' => 'blocked',
        ]);
    });

    return back()->with('success', 'Slot blocked successfully.');
}

public function unblockSlot(TemplePoojaSchedule $schedule,BookingSlot $slot) {
    DB::transaction(function () use ($schedule, $slot) {
        $slot = BookingSlot::query()
            ->where('id', $slot->id)
            ->where('temple_pooja_schedule_id', $schedule->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($schedule->status !== 'active') {
            throw ValidationException::withMessages([
                'slot' => 'This slot cannot be unblocked because its schedule is inactive.'
            ]);
        }

        if ($slot->status !== 'blocked') {
            return;
        }

        $slot->update([
            'status' => 'available',
        ]);
    });

    return back()->with('success', 'Slot unblocked successfully.');
}

}

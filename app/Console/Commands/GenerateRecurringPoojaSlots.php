<?php

namespace App\Console\Commands;

use App\Models\TemplePoojaSchedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringPoojaSlots extends Command
{
    protected $signature = 'pooja:generate-recurring-slots';

    protected $description = 'Generate missing booking slots for active recurring Pooja schedules';

    public function handle(): int
    {
        $today = Carbon::today('Asia/Kolkata');
        $endDate = $today->copy()->addDays(89);

        $totalInserted = 0;
        $totalSchedules = 0;

        TemplePoojaSchedule::query()
            ->where('schedule_type', 'recurring')
            ->where('status', 'active')
            ->whereNotNull('pooja_id')
            ->chunkById(100, function ($schedules) use (
                $today,
                $endDate,
                &$totalInserted,
                &$totalSchedules
            ) {
                foreach ($schedules as $schedule) {
                    $totalSchedules++;

                    $timezone = $schedule->timezone ?: 'Asia/Kolkata';
                    $duration = (int) $schedule->slot_duration_minutes;

                    if ($duration < 1) {
                        $this->warn("Skipped schedule #{$schedule->id}: invalid slot duration.");
                        continue;
                    }

                    $startDate = $today->copy()->setTimezone($timezone);
                    $lastDate = $endDate->copy()->setTimezone($timezone);

                    $dates = CarbonPeriod::create($startDate, $lastDate);

                    foreach ($dates as $date) {
                        if ($date->dayOfWeek !== (int) $schedule->day_of_week) {
                            continue;
                        }

                        $start = Carbon::parse(
                            $date->toDateString() . ' ' . $schedule->start_time,
                            $timezone
                        );

                        $end = Carbon::parse(
                            $date->toDateString() . ' ' . $schedule->end_time,
                            $timezone
                        );

                        $rows = [];

                        while (
                            $start->copy()
                                ->addMinutes($duration)
                                ->lessThanOrEqualTo($end)
                        ) {
                            $slotEnd = $start->copy()->addMinutes($duration);

                            $rows[] = [
                                'temple_pooja_schedule_id' => $schedule->id,
                                'slot_date' => $date->toDateString(),
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
                            $totalInserted += DB::table('booking_slots')
                                ->insertOrIgnore($chunk);
                        }
                    }
                }
            });

        $this->info("Recurring schedules processed: {$totalSchedules}");
        $this->info("New booking slots inserted: {$totalInserted}");

        return self::SUCCESS;
    }
}

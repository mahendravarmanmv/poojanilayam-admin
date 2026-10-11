<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'temple_pooja_schedule_id',
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'booked_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'slot_date' => 'date',
            'capacity' => 'integer',
            'booked_count' => 'integer',
        ];
    }

    public function templePoojaSchedule(): BelongsTo
    {
        return $this->belongsTo(TemplePoojaSchedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function locks(): HasMany
    {
        return $this->hasMany(BookingSlotLock::class);
    }

    public function oldReschedules(): HasMany
    {
        return $this->hasMany(BookingReschedule::class, 'old_booking_slot_id');
    }

    public function newReschedules(): HasMany
    {
        return $this->hasMany(BookingReschedule::class, 'new_booking_slot_id');
    }
}

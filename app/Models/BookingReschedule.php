<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'old_booking_slot_id',
        'new_booking_slot_id',
        'requested_by_user_id',
        'status',
        'reason',
        'requested_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function oldBookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class, 'old_booking_slot_id');
    }

    public function newBookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class, 'new_booking_slot_id');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }
}

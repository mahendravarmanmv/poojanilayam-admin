<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'temple_pooja_id',
        'booking_slot_id',
        'currency_id',
        'booking_id',
        'reference_id',
        'service_mode',
        'status',
        'booking_date',
        'start_time',
        'end_time',
        'timezone',
        'pooja_amount',
        'extras_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_confirmed_at',
        'confirmed_at',
        'completed_at',
        'cancelled_at',
        'customer_notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'pooja_amount' => 'decimal:2',
            'extras_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'payment_confirmed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function templePooja(): BelongsTo
    {
        return $this->belongsTo(TemplePooja::class);
    }

    public function bookingSlot(): BelongsTo
    {
        return $this->belongsTo(BookingSlot::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function slotLocks(): HasMany
    {
        return $this->hasMany(BookingSlotLock::class, 'booking_slot_id', 'booking_slot_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BookingAssignment::class);
    }

    public function sankalpam(): HasOne
    {
        return $this->hasOne(BookingSankalpam::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(BookingExtra::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(BookingAddress::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(BookingCancellation::class);
    }

    public function reschedules(): HasMany
    {
        return $this->hasMany(BookingReschedule::class);
    }
}

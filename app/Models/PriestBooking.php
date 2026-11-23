<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PriestBooking extends Model
{
    protected $fillable = [
        'booking_number',
        'pujari_profile_id',
        'pujari_service_id',
        'pujari_availability_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'address',
        'city',
        'state',
        'pincode',
        'service_name',
        'service_amount',
        'tax_details',
        'tax_amount',
        'total_amount',
        'booking_status',
        'payment_status',
        'payment_expires_at',
        'terms_accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'duration_minutes' => 'integer',
            'service_amount' => 'decimal:2',
            'tax_details' => 'array',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'payment_expires_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class);
    }

    public function pujariService(): BelongsTo
    {
        return $this->belongsTo(PujariService::class);
    }

    public function pujariAvailability(): BelongsTo
    {
        return $this->belongsTo(PujariAvailability::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            PriestBookingPayment::class,
            'priest_booking_id'
        );
    }
}
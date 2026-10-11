<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyBooking extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(AstrologerProfile::class, 'astrologer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(AstrologyService::class, 'astrology_service_id');
    }

    public function consultation(): HasOne
    {
        return $this->hasOne(AstrologyConsultation::class, 'astrology_booking_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(AstrologyReport::class, 'astrology_booking_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(AstrologyBookingStatusHistory::class, 'astrology_booking_id');
    }
}
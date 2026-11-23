<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriestBookingPayment extends Model
{
    protected $fillable = [
        'priest_booking_id',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'payment_session_id',
        'amount',
        'currency',
        'payment_status',
        'gateway_response',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function priestBooking(): BelongsTo
    {
        return $this->belongsTo(
            PriestBooking::class,
            'priest_booking_id'
        );
    }
}
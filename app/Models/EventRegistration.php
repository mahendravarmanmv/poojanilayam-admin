<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'event_id', 'customer_profile_id', 'registration_reference',
        'ticket_quantity', 'participant_count', 'participant_details',
        'payment_required', 'amount', 'currency_id', 'payment_status',
        'status', 'notes', 'registered_at', 'confirmed_at', 'cancelled_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'ticket_quantity' => 'integer',
            'participant_count' => 'integer',
            'participant_details' => 'array',
            'payment_required' => 'boolean',
            'amount' => 'decimal:2',
            'registered_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(TempleEvent::class, 'event_id');
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_profile_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}

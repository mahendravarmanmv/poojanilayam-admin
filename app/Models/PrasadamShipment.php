<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrasadamShipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'prasadam_order_id',
        'shipment_id',
        'carrier_name',
        'service_name',
        'tracking_number',
        'status',
        'tracking_url',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'estimated_delivery_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function prasadamOrder(): BelongsTo
    {
        return $this->belongsTo(PrasadamOrder::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(PrasadamTrackingEvent::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'shipment_id',
        'status',
        'carrier_name',
        'service_name',
        'tracking_number',
        'tracking_url',
        'shipping_method',
        'recipient_name',
        'recipient_phone',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'country',
        'postal_code',
        'shipping_charge',
        'packed_at',
        'shipped_at',
        'estimated_delivery_at',
        'delivered_at',
        'returned_at',
        'cancelled_at',
        'delivery_notes',
    ];

    protected $casts = [
        'shipping_charge' => 'decimal:2',
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'estimated_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'returned_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(ShipmentTrackingEvent::class);
    }

    public function deliveryPartnerAssignments(): HasMany
    {
        return $this->hasMany(DeliveryPartnerAssignment::class);
    }
}

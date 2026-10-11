<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PrasadamOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'booking_id',
        'temple_id',
        'prasadam_order_id',
        'status',
        'delivery_type',
        'recipient_name',
        'recipient_mobile',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'country',
        'postal_code',
        'prepared_at',
        'dispatched_at',
        'delivered_at',
        'cancelled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'prepared_at' => 'datetime',
            'dispatched_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrasadamOrderItem::class);
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(PrasadamDispatch::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(PrasadamShipment::class);
    }
}

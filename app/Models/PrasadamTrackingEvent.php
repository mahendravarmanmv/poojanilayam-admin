<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrasadamTrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'prasadam_shipment_id',
        'status',
        'location',
        'description',
        'event_at',
    ];

    protected function casts(): array
    {
        return [
            'event_at' => 'datetime',
        ];
    }

    public function prasadamShipment(): BelongsTo
    {
        return $this->belongsTo(PrasadamShipment::class);
    }
}

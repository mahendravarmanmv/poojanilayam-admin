<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'reference_type',
        'reference_id',
        'quantity',
        'status',
        'reserved_at',
        'expires_at',
        'released_at',
        'converted_at',
    ];

    protected $casts = [
        'reference_id' => 'integer',
        'quantity' => 'integer',
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}

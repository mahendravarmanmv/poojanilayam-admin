<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplePoojaPricing extends Model
{
    use HasFactory;

    protected $fillable = [
        'temple_pooja_id',
        'currency_id',
        'pricing_type',
        'amount',
        'discount_amount',
        'effective_from',
        'effective_until',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function templePooja(): BelongsTo
    {
        return $this->belongsTo(TemplePooja::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

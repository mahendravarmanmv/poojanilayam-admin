<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaPricing extends Model
{
    use HasFactory;

    /**
     * The database table is intentionally singular.
     * The migration creates `pooja_pricing`, not Laravel's default
     * pluralized `pooja_pricings`.
     */
    protected $table = 'pooja_pricing';

    protected $fillable = [
        'pooja_id',
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

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}

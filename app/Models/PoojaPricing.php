<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaPricing extends Model
{
    use HasFactory;

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

    protected $casts = [
        'amount'          => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'is_default'      => 'boolean',
        'is_active'       => 'boolean',
        'effective_from'  => 'datetime',
        'effective_until' => 'datetime',
    ];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
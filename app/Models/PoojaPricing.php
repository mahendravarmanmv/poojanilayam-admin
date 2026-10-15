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
        'title',
        'price',
        'special_price',
        'description',
        'includes_samagri',
        'is_default',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'special_price' => 'decimal:2',
        'includes_samagri' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductTaxRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'tax_code', 'tax_name', 'tax_percentage',
        'effective_from', 'effective_until', 'is_inclusive', 'active',
    ];

    protected function casts(): array
    {
        return [
            'tax_percentage' => 'decimal:3',
            'effective_from' => 'datetime',
            'effective_until' => 'datetime',
            'is_inclusive' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

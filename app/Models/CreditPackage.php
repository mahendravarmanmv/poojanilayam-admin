<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_code', 'name', 'description', 'credit_amount',
        'purchase_amount', 'currency_code', 'is_active', 'sort_order',
        'starts_at', 'ends_at',
    ];

    protected $casts = [
        'credit_amount' => 'decimal:2',
        'purchase_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function creditPurchases(): HasMany
    {
        return $this->hasMany(CreditPurchase::class);
    }
}

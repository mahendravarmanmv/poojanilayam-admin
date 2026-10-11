<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartCheckoutSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'customer_profile_id',
        'checkout_token',
        'status',
        'currency_code',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'cart_snapshot',
        'pricing_snapshot',
        'tax_snapshot',
        'expires_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'cart_snapshot' => 'array',
        'pricing_snapshot' => 'array',
        'tax_snapshot' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}

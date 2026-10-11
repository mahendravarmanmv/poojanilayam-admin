<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPurchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id', 'wallet_id', 'credit_package_id', 'payment_id',
        'purchase_id', 'credit_amount', 'purchase_amount', 'currency_code',
        'status', 'paid_at', 'credited_at', 'refunded_at', 'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'credit_amount' => 'decimal:2',
        'purchase_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'credited_at' => 'datetime',
        'refunded_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function creditPackage(): BelongsTo
    {
        return $this->belongsTo(CreditPackage::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}

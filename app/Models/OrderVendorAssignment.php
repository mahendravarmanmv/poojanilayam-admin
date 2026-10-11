<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderVendorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'vendor_profile_id',
        'status',
        'accepted_at',
        'dispatched_at',
        'completed_at',
        'remarks',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderVendorItem::class);
    }
}

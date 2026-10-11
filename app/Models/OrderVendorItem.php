<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderVendorItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_vendor_assignment_id',
        'order_item_id',
        'quantity',
        'status',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function orderVendorAssignment(): BelongsTo
    {
        return $this->belongsTo(OrderVendorAssignment::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }
}

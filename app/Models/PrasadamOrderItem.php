<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrasadamOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prasadam_order_id',
        'prasadam_item_id',
        'item_name',
        'quantity',
        'unit',
        'weight',
        'weight_unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight' => 'decimal:3',
        ];
    }

    public function prasadamOrder(): BelongsTo
    {
        return $this->belongsTo(PrasadamOrder::class);
    }

    public function prasadamItem(): BelongsTo
    {
        return $this->belongsTo(PrasadamItem::class);
    }
}

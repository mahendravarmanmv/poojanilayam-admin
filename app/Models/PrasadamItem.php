<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrasadamItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prasadam_code',
        'name',
        'slug',
        'description',
        'prasadam_type',
        'unit',
        'weight',
        'weight_unit',
        'requires_shipping',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:3',
            'requires_shipping' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(PrasadamOrderItem::class);
    }
}

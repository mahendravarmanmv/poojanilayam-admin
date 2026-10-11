<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pooja_id',
        'name',
        'quantity',
        'unit',
        'requirement_type',
        'description',
        'sort_order',
        'is_optional',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'sort_order' => 'integer',
            'is_optional' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }
}

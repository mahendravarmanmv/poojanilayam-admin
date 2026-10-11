<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplePoojaSamagri extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'temple_pooja_id',
        'name',
        'quantity',
        'unit',
        'description',
        'is_optional',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'is_optional' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function templePooja(): BelongsTo
    {
        return $this->belongsTo(TemplePooja::class);
    }
}

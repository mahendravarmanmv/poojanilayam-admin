<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class God extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'god_code',
        'name',
        'slug',
        'short_description',
        'description',
        'image_path',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function poojas(): BelongsToMany
    {
        return $this->belongsToMany(
            Pooja::class,
            'god_poojas',
            'god_id',
            'pooja_id'
        )->withPivot([
            'sort_order',
            'is_primary',
            'is_active',
        ])->withTimestamps();
    }

    public function godPoojas(): HasMany
    {
        return $this->hasMany(GodPooja::class);
    }
}

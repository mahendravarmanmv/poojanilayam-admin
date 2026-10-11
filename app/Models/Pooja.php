<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pooja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pooja_code',
        'pooja_category_id',
        'name',
        'slug',
        'short_description',
        'description',
        'duration_minutes',
        'status',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoojaCategory::class, 'pooja_category_id');
    }

    public function gods(): BelongsToMany
    {
        return $this->belongsToMany(
            God::class,
            'god_poojas',
            'pooja_id',
            'god_id'
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

    public function detail(): HasOne
    {
        return $this->hasOne(PoojaDetail::class);
    }

    public function pricing(): HasMany
    {
        return $this->hasMany(PoojaPricing::class);
    }

    public function extras(): HasMany
    {
        return $this->hasMany(PoojaExtra::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PoojaMedia::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(PoojaRequirement::class);
    }

    public function templePoojas(): HasMany
    {
        return $this->hasMany(TemplePooja::class);
    }
}

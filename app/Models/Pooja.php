<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pooja extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'poojas';

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

    protected $casts = [
        'is_featured'      => 'boolean',
        'is_active'        => 'boolean',
        'duration_minutes' => 'integer',
        'sort_order'       => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoojaCategory::class, 'pooja_category_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(PoojaDetail::class, 'pooja_id');
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(PoojaPricing::class, 'pooja_id');
    }

    // Alias for compatibility
    public function pricing(): HasMany
    {
        return $this->hasMany(PoojaPricing::class, 'pooja_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PoojaMedia::class, 'pooja_id')->orderBy('sort_order');
    }

    public function primaryMedia(): HasOne
    {
        return $this->hasOne(PoojaMedia::class, 'pooja_id')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order');
    }

    public function gods(): BelongsToMany
    {
        return $this->belongsToMany(God::class, 'god_poojas', 'pooja_id', 'god_id');
    }
}
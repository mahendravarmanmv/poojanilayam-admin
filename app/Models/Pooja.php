<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pooja extends Model
{
    use HasFactory;

    protected $table = 'poojas';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'is_popular',
        'is_temple_pooja',
        'status', // 'active', 'inactive'
    ];

    protected $casts = [
        'is_popular' => 'boolean',
        'is_temple_pooja' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoojaCategory::class, 'category_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(PoojaDetail::class, 'pooja_id');
    }

    public function pricings(): HasMany
    {
        return $this->hasMany(PoojaPricing::class, 'pooja_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PoojaMedia::class, 'pooja_id');
    }

    public function primaryMedia(): HasOne
    {
        return $this->hasOne(PoojaMedia::class, 'pooja_id')->where('is_primary', true);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'is_active',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function price(): HasOne
    {
        return $this->hasOne(ProductPrice::class)->where('is_default', true)->withDefault([
            'price' => 0.00,
        ]);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class)->withDefault([
            'quantity_on_hand' => 0,
        ]);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
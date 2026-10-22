<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_category_id', 'product_code', 'sku', 'name', 'slug',
        'short_description', 'description', 'product_type', 'brand',
        'unit', 'weight', 'weight_unit', 'is_digital', 'featured', 'active', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['weight' => 'decimal:3', 'is_digital' => 'boolean', 'featured' => 'boolean', 'active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function category(): BelongsTo { return $this->belongsTo(ProductCategory::class, 'product_category_id'); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class); }
    public function prices(): HasMany { return $this->hasMany(ProductPrice::class); }
    public function inventories(): HasMany { return $this->hasMany(Inventory::class); }
}

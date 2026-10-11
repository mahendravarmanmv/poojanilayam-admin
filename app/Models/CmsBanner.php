<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsBanner extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_banners';

    protected $fillable = [
        'banner_code', 'title', 'subtitle', 'banner_type', 'image_path',
        'mobile_image_path', 'button_text', 'button_url', 'starts_at', 'ends_at',
        'sort_order', 'featured', 'active', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime', 'ends_at' => 'datetime',
            'sort_order' => 'integer', 'featured' => 'boolean',
            'active' => 'boolean', 'metadata' => 'array',
        ];
    }
}

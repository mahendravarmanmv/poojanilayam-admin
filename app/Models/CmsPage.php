<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_pages';

    protected $fillable = [
        'page_code', 'title', 'slug', 'page_type', 'excerpt', 'content',
        'status', 'is_homepage', 'active', 'sort_order', 'published_at',
        'unpublished_at', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_homepage' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function sections(): HasMany { return $this->hasMany(CmsPageSection::class); }
    public function media(): HasMany { return $this->hasMany(CmsPageMedia::class); }
    public function menuItems(): HasMany { return $this->hasMany(CmsMenuItem::class); }
}

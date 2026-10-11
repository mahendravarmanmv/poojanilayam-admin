<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blog_code', 'blog_category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image_path', 'status', 'featured', 'active', 'author_user_id',
        'published_at', 'unpublished_at', 'meta_title', 'meta_description',
        'canonical_url', 'metadata',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'published_at' => 'datetime',
        'unpublished_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function tagAssignments(): HasMany
    {
        return $this->hasMany(BlogTagAssignment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            BlogTag::class,
            'blog_tag_assignments',
            'blog_id',
            'blog_tag_id'
        )->withTimestamps();
    }

    public function media(): HasMany
    {
        return $this->hasMany(BlogMedia::class);
    }
}

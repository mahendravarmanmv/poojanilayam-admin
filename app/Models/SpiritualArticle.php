<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiritualArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_code', 'title', 'slug', 'excerpt', 'content', 'featured_image_path',
        'status', 'featured', 'active', 'author_user_id', 'published_at',
        'meta_title', 'meta_description', 'metadata',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }
}

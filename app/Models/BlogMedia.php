<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blog_id', 'media_type', 'file_path', 'file_name', 'mime_type',
        'file_size', 'alt_text', 'title', 'sort_order', 'featured', 'active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}

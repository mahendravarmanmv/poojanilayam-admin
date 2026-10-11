<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiritualVideo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'video_code', 'title', 'slug', 'description', 'thumbnail_path',
        'video_source', 'video_url', 'file_path', 'duration_seconds', 'status',
        'featured', 'active', 'created_by_user_id', 'published_at', 'metadata',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'featured' => 'boolean',
        'active' => 'boolean',
        'published_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

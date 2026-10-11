<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLibrary extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_library';

    protected $fillable = [
        'uploaded_by_user_id', 'media_type', 'disk', 'file_path', 'file_name',
        'original_name', 'file_extension', 'mime_type', 'file_size', 'width',
        'height', 'duration_seconds', 'folder', 'title', 'alt_text', 'caption',
        'description', 'metadata', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'duration_seconds' => 'integer',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}

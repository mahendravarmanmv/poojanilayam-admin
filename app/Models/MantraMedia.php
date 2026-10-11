<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantraMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'mantra_id', 'language_id', 'media_type', 'file_path', 'media_url',
        'title', 'duration_seconds', 'sort_order', 'active',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];

    public function mantra(): BelongsTo
    {
        return $this->belongsTo(Mantra::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

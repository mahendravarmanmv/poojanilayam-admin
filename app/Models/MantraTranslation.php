<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantraTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mantra_id', 'language_id', 'title', 'lyrics', 'meaning', 'is_default', 'active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
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

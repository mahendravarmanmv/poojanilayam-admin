<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariLanguage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pujari_profile_id',
        'language_id',
        'proficiency',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

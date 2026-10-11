<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaSankalpam extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_id',
        'language_id',
        'devotee_names',
        'family_names',
        'gotram',
        'nakshatram',
        'purpose',
        'special_instructions',
        'source_text',
        'generated_text',
    ];

    public function digitalPooja(): BelongsTo
    {
        return $this->belongsTo(DigitalPooja::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

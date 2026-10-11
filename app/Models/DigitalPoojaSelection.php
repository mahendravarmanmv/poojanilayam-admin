<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_id',
        'god_id',
        'language_id',
        'flower_selection',
        'deepam_selection',
    ];

    public function digitalPooja(): BelongsTo
    {
        return $this->belongsTo(DigitalPooja::class);
    }

    public function god(): BelongsTo
    {
        return $this->belongsTo(God::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}

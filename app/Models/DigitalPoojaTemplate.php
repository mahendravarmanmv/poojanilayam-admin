<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DigitalPoojaTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pooja_id',
        'template_code',
        'name',
        'description',
        'version',
        'is_default',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(DigitalPoojaTemplateStep::class);
    }

    public function aiGenerations(): HasMany
    {
        return $this->hasMany(DigitalPoojaAiGeneration::class, 'template_id');
    }

    public function digitalPoojas(): HasMany
    {
        return $this->hasMany(DigitalPooja::class, 'template_id');
    }
}

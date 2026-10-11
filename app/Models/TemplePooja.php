<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplePooja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'temple_id',
        'pooja_id',
        'temple_pooja_code',
        'service_mode',
        'live_available',
        'recording_available',
        'prasadam_available',
        'sort_order',
        'status',
        'description',
        'special_instructions',
    ];

    protected function casts(): array
    {
        return [
            'live_available' => 'boolean',
            'recording_available' => 'boolean',
            'prasadam_available' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }

    public function pricing(): HasMany
    {
        return $this->hasMany(TemplePoojaPricing::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TemplePoojaSchedule::class);
    }

    public function samagri(): HasMany
    {
        return $this->hasMany(TemplePoojaSamagri::class);
    }
}

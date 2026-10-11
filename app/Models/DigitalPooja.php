<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DigitalPooja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'customer_profile_id',
        'pooja_id',
        'template_id',
        'digital_pooja_id',
        'status',
        'payment_confirmed_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'payment_confirmed_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DigitalPoojaTemplate::class, 'template_id');
    }

    public function selections(): HasOne
    {
        return $this->hasOne(DigitalPoojaSelection::class);
    }

    public function personalizations(): HasMany
    {
        return $this->hasMany(DigitalPoojaPersonalization::class);
    }

    public function sankalpam(): HasOne
    {
        return $this->hasOne(DigitalPoojaSankalpam::class);
    }

    public function aiGenerations(): HasMany
    {
        return $this->hasMany(DigitalPoojaAiGeneration::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(DigitalPoojaCertificate::class);
    }
}

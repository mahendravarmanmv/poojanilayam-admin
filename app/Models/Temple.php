<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Temple extends Model
{
    protected $guarded = ['id'];

    public function profile(): HasOne
    {
        return $this->hasOne(TempleProfile::class, 'temple_id')->withDefault();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function timings(): HasMany
    {
        return $this->hasMany(TempleTiming::class, 'temple_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TempleEvent::class, 'temple_id');
    }

    public function poojas(): HasMany
    {
        return $this->hasMany(TemplePooja::class, 'temple_id');
    }

    public function pujaris(): HasMany
    {
        return $this->hasMany(PujariTempleAssignment::class, 'temple_id');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(TempleGallery::class, 'temple_id');
    }
}
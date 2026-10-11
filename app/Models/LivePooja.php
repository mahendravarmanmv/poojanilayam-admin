<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LivePooja extends Model
{
    protected $guarded = ['id'];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }

    public function pujari(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class, 'pujari_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(LivePoojaSession::class, 'live_pooja_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LivePoojaParticipant::class, 'live_pooja_id');
    }
}
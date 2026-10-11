<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PujariProfile extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(PujariBankAccount::class, 'pujari_profile_id');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(PujariBankAccount::class, 'pujari_profile_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PujariDocument::class, 'pujari_profile_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(PujariVerification::class, 'pujari_profile_id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(PujariLanguage::class, 'pujari_profile_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(PujariExperience::class, 'pujari_profile_id');
    }

    public function templeAssignments(): HasMany
    {
        return $this->hasMany(PujariTempleAssignment::class, 'pujari_profile_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(PujariAvailability::class, 'pujari_profile_id');
    }
}
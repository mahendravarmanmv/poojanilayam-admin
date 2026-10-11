<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerProfile extends Model
{
    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(AstrologerBankAccount::class, 'astrologer_profile_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AstrologerDocument::class, 'astrologer_profile_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(AstrologerVerification::class, 'astrologer_profile_id');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(AstrologerLanguage::class, 'astrologer_profile_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(AstrologerExperience::class, 'astrologer_profile_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(AstrologerService::class, 'astrologer_profile_id');
    }
}
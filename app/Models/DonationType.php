<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type_code',
        'name',
        'slug',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(DonationCampaign::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}

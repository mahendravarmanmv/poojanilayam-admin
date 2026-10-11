<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'donation_type_id',
        'temple_id',
        'campaign_code',
        'name',
        'slug',
        'description',
        'short_description',
        'target_amount',
        'collected_amount',
        'status',
        'starts_at',
        'ends_at',
        'featured',
        'active',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'featured' => 'boolean',
        'active' => 'boolean',
    ];

    public function donationType(): BelongsTo
    {
        return $this->belongsTo(DonationType::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}

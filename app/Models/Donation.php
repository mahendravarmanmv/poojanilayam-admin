<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'donation_type_id',
        'donation_campaign_id',
        'temple_id',
        'donation_id',
        'reference_id',
        'amount',
        'currency_code',
        'currency_id',
        'status',
        'donor_name',
        'donor_email',
        'donor_mobile',
        'is_anonymous',
        'purpose',
        'donor_message',
        'payment_reference',
        'paid_at',
        'cancelled_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'currency_id' => 'integer',
        'is_anonymous' => 'boolean',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function donationType(): BelongsTo
    {
        return $this->belongsTo(DonationType::class);
    }

    public function donationCampaign(): BelongsTo
    {
        return $this->belongsTo(DonationCampaign::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(DonationStatusHistory::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(DonationReceipt::class);
    }
}

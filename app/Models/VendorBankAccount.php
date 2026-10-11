<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorBankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_profile_id', 'account_holder_name', 'account_number',
        'ifsc_code', 'bank_name', 'branch_name', 'status', 'is_default',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(VendorBankVerification::class);
    }
}

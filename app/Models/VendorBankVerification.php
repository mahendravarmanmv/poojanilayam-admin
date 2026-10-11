<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_bank_account_id', 'verified_by_user_id',
        'verification_method', 'status', 'remarks', 'verified_at',
    ];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function vendorBankAccount(): BelongsTo
    {
        return $this->belongsTo(VendorBankAccount::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}

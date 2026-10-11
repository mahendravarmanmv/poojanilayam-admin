<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAddress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_profile_id', 'address_type', 'address_line_1',
        'address_line_2', 'landmark', 'city', 'state', 'country',
        'postal_code', 'contact_name', 'contact_mobile', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }
}

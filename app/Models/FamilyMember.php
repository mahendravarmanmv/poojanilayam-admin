<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_profile_id',
        'relation_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'gotram',
        'nakshatra',
        'rashi',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(FamilyMemberRelation::class, 'relation_id');
    }
}

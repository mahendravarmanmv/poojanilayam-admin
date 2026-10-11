<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSankalpamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_sankalpam_id',
        'family_member_id',
        'name',
        'relationship',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function bookingSankalpam(): BelongsTo
    {
        return $this->belongsTo(BookingSankalpam::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(FamilyMember::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariAvailability extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pujari_profile_id',
        'availability_type',
        'day_of_week',
        'availability_date',
        'start_time',
        'end_time',
        'status',
        'timezone',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'availability_date' => 'date',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class);
    }
}

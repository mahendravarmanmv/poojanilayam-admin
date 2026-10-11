<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomePoojaBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'pooja_id',
        'samagri_required',
        'samagri_notes',
        'service_instructions',
    ];

    protected function casts(): array
    {
        return [
            'samagri_required' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }
}

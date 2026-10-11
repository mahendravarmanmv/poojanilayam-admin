<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingExtra extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'pooja_extra_id',
        'pooja_extra_option_id',
        'name',
        'code',
        'quantity',
        'unit_price',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function poojaExtra(): BelongsTo
    {
        return $this->belongsTo(PoojaExtra::class);
    }

    public function poojaExtraOption(): BelongsTo
    {
        return $this->belongsTo(PoojaExtraOption::class);
    }
}

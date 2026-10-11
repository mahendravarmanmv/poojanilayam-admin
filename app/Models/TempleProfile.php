<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TempleProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'temple_id',
        'short_description',
        'full_description',
        'address_id',
        'website',
        'email',
        'phone',
        'established_year',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'established_year' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}

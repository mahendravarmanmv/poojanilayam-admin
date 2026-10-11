<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pooja_id',
        'benefits',
        'procedure',
        'special_instructions',
        'eligibility',
        'notes',
    ];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }
}

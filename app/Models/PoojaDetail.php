<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaDetail extends Model
{
    use HasFactory;

    protected $table = 'pooja_details';

    protected $fillable = [
        'pooja_id',
        'significance',
        'procedure',
        'duration_minutes',
        'samagri_list',
        'instructions',
    ];

    protected $casts = [
        'samagri_list' => 'array',
        'duration_minutes' => 'integer',
    ];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }
}
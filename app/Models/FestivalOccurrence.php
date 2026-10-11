<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FestivalOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'festival_id', 'occurrence_date', 'start_time', 'end_time', 'timezone',
        'location', 'notes', 'active',
    ];

    protected $casts = [
        'occurrence_date' => 'date',
        'active' => 'boolean',
    ];

    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }
}

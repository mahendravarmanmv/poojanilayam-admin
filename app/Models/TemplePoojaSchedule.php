<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplePoojaSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'temple_pooja_id',
        'schedule_type',
        'day_of_week',
        'schedule_date',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'capacity',
        'status',
        'timezone',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'schedule_date' => 'date',
            'slot_duration_minutes' => 'integer',
            'capacity' => 'integer',
        ];
    }

    public function templePooja(): BelongsTo
    {
        return $this->belongsTo(TemplePooja::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariTempleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pujari_profile_id',
        'temple_id',
        'status',
        'assigned_at',
        'ended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }
}

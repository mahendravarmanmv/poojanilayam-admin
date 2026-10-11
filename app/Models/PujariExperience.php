<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariExperience extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pujari_profile_id',
        'title',
        'temple_or_organization',
        'experience_years',
        'started_on',
        'ended_on',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'started_on' => 'date',
            'ended_on' => 'date',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariAvailability extends Model
{
    use HasFactory;

    protected $table = 'pujari_availabilities';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'day_of_week'       => 'integer',
            'availability_date' => 'date',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(PujariProfile::class, 'pujari_profile_id');
    }
}
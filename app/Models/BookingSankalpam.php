<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingSankalpam extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'language_id',
        'purpose_type',
        'purpose_details',
        'gotram',
        'nakshatram',
        'special_instructions',
        'sankalpam_text',
        'generated_text',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(BookingSankalpamMember::class);
    }
}

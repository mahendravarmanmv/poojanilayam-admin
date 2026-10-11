<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaPersonalization extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_id',
        'field_key',
        'field_label',
        'field_value',
        'field_type',
        'is_required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function digitalPooja(): BelongsTo
    {
        return $this->belongsTo(DigitalPooja::class);
    }
}

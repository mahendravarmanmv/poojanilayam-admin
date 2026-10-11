<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaAiGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_id',
        'template_id',
        'generation_type',
        'status',
        'input_summary',
        'generated_content',
        'provider',
        'model',
        'request_id',
        'error_message',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function digitalPooja(): BelongsTo
    {
        return $this->belongsTo(DigitalPooja::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DigitalPoojaTemplate::class, 'template_id');
    }
}

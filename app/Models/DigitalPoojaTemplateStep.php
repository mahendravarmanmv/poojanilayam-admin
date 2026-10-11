<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaTemplateStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_template_id',
        'step_code',
        'step_type',
        'title',
        'content',
        'media_path',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DigitalPoojaTemplate::class, 'digital_pooja_template_id');
    }
}

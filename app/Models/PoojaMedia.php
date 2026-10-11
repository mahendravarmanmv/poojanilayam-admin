<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pooja_id',
        'media_type',
        'file_path',
        'title',
        'description',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class);
    }
}

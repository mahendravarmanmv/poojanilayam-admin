<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoojaMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pooja_media';

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

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
        'sort_order'  => 'integer',
    ];

    public function pooja(): BelongsTo
    {
        return $this->belongsTo(Pooja::class, 'pooja_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Festival extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'festival_code', 'name', 'slug', 'short_description', 'description',
        'start_date', 'end_date', 'image_path', 'banner_path', 'status',
        'featured', 'active', 'sort_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'featured' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function occurrences(): HasMany
    {
        return $this->hasMany(FestivalOccurrence::class);
    }
}

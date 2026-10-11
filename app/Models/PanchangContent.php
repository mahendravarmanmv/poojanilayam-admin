<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanchangContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_code', 'panchang_date', 'location_name', 'timezone', 'title',
        'content', 'panchang_data', 'status', 'active', 'published_at',
    ];

    protected $casts = [
        'panchang_date' => 'date',
        'panchang_data' => 'array',
        'active' => 'boolean',
        'published_at' => 'datetime',
    ];
}

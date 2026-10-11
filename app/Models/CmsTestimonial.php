<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsTestimonial extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_testimonials';

    protected $fillable = [
        'testimonial_code', 'name', 'designation', 'location', 'testimonial',
        'photo_path', 'rating', 'status', 'sort_order', 'featured',
        'active', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer', 'sort_order' => 'integer',
            'featured' => 'boolean', 'active' => 'boolean', 'published_at' => 'datetime',
        ];
    }
}

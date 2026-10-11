<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsFaq extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_faqs';

    protected $fillable = [
        'faq_code', 'question', 'answer', 'category', 'status',
        'sort_order', 'featured', 'active',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'featured' => 'boolean', 'active' => 'boolean'];
    }
}

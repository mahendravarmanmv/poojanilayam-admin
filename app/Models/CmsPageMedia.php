<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsPageMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_page_media';

    protected $fillable = [
        'cms_page_id', 'cms_page_section_id', 'media_type', 'file_path',
        'file_name', 'mime_type', 'file_size', 'alt_text', 'title',
        'sort_order', 'featured', 'active',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer', 'sort_order' => 'integer',
            'featured' => 'boolean', 'active' => 'boolean',
        ];
    }

    public function page(): BelongsTo { return $this->belongsTo(CmsPage::class, 'cms_page_id'); }
    public function section(): BelongsTo { return $this->belongsTo(CmsPageSection::class, 'cms_page_section_id'); }
}

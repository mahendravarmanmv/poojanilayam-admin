<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPageSection extends Model
{
    use HasFactory;

    protected $table = 'cms_page_sections';

    protected $fillable = [
        'cms_page_id', 'section_key', 'section_type', 'title', 'subtitle',
        'content', 'status', 'sort_order', 'settings',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'settings' => 'array'];
    }

    public function page(): BelongsTo { return $this->belongsTo(CmsPage::class, 'cms_page_id'); }
    public function media(): HasMany { return $this->hasMany(CmsPageMedia::class, 'cms_page_section_id'); }
}

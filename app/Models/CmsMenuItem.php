<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenuItem extends Model
{
    use HasFactory;

    protected $table = 'cms_menu_items';

    protected $fillable = [
        'cms_menu_id', 'parent_id', 'cms_page_id', 'label', 'url', 'target',
        'icon', 'sort_order', 'active',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'active' => 'boolean'];
    }

    public function menu(): BelongsTo { return $this->belongsTo(CmsMenu::class, 'cms_menu_id'); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function page(): BelongsTo { return $this->belongsTo(CmsPage::class, 'cms_page_id'); }
}

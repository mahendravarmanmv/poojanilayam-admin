<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    use HasFactory;

    protected $table = 'cms_menus';

    protected $fillable = ['menu_code', 'name', 'location', 'status', 'active'];

    protected function casts(): array { return ['active' => 'boolean']; }

    public function items(): HasMany { return $this->hasMany(CmsMenuItem::class, 'cms_menu_id'); }
}

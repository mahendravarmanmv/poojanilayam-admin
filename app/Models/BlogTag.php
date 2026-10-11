<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function assignments(): HasMany
    {
        return $this->hasMany(BlogTagAssignment::class);
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(
            Blog::class,
            'blog_tag_assignments',
            'blog_tag_id',
            'blog_id'
        )->withTimestamps();
    }
}

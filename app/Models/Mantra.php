<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mantra extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mantra_code', 'title', 'slug', 'short_description', 'status',
        'featured', 'active', 'sort_order',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(MantraTranslation::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(MantraMedia::class);
    }

    public function userFavorites(): HasMany
    {
        return $this->hasMany(UserMantraFavorite::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_mantra_favorites',
            'mantra_id',
            'user_id'
        )->withPivot('created_at');
    }
}

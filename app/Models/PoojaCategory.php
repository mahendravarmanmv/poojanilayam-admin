<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoojaCategory extends Model
{
    use HasFactory;

    protected $table = 'pooja_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'status',
    ];

    public function poojas(): HasMany
    {
        return $this->hasMany(Pooja::class, 'category_id');
    }
}
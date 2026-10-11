<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'iso_code', 'phone_code', 'currency_id', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function states(): HasMany { return $this->hasMany(State::class); }
    public function cities(): HasMany { return $this->hasMany(City::class); }
    public function addresses(): HasMany { return $this->hasMany(Address::class); }
}

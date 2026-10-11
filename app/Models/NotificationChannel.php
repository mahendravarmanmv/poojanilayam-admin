<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_code',
        'name',
        'supports_template',
        'active',
        'sort_order',
    ];

    protected $casts = [
        'supports_template' => 'boolean',
        'active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(NotificationTemplate::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(NotificationDelivery::class);
    }
}

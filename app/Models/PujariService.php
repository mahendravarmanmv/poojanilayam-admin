<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PujariService extends Model
{
    protected $table = 'pujari_services';

    protected $fillable = [
        'pujari_profile_id',
        'service_name',
        'description',
        'price',
		'duration_minutes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
			'duration_minutes' => 'integer',
        ];
    }

    public function pujariProfile(): BelongsTo
    {
        return $this->belongsTo(
            PujariProfile::class,
            'pujari_profile_id'
        );
    }
}

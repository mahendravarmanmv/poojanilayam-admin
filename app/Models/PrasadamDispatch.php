<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrasadamDispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'prasadam_order_id',
        'temple_id',
        'dispatched_by_user_id',
        'dispatch_id',
        'status',
        'prepared_at',
        'dispatched_at',
        'package_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'prepared_at' => 'datetime',
            'dispatched_at' => 'datetime',
        ];
    }

    public function prasadamOrder(): BelongsTo
    {
        return $this->belongsTo(PrasadamOrder::class);
    }

    public function temple(): BelongsTo
    {
        return $this->belongsTo(Temple::class);
    }

    public function dispatchedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by_user_id');
    }
}

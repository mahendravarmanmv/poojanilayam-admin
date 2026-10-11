<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePoojaParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_pooja_session_id',
        'user_id',
        'participant_type',
        'display_name',
        'email',
        'mobile',
        'authorization_status',
        'joined_at',
        'left_at',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    public function livePoojaSession(): BelongsTo
    {
        return $this->belongsTo(LivePoojaSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

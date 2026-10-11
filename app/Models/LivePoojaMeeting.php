<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePoojaMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_pooja_session_id',
        'provider',
        'meeting_id',
        'meeting_code',
        'meeting_url',
        'host_url',
        'calendar_event_id',
        'calendar_invite_url',
        'calendar_invite_file_path',
        'active',
        'valid_from',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function livePoojaSession(): BelongsTo
    {
        return $this->belongsTo(LivePoojaSession::class);
    }
}

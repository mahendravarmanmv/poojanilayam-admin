<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePoojaReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_pooja_session_id',
        'reminder_type',
        'scheduled_at',
        'sent_at',
        'status',
        'attempts',
        'channel',
        'notification_reference',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'attempts' => 'integer',
        ];
    }

    public function livePoojaSession(): BelongsTo
    {
        return $this->belongsTo(LivePoojaSession::class);
    }
}

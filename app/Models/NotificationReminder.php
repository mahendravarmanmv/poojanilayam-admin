<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_id',
        'user_id',
        'remindable_type',
        'remindable_id',
        'event_type',
        'reminder_type',
        'scheduled_at',
        'sent_at',
        'status',
        'deduplication_key',
        'payload',
        'failure_reason',
    ];

    protected $casts = [
        'remindable_id' => 'integer',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function remindable(): MorphTo
    {
        return $this->morphTo();
    }
}

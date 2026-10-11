<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'notification_channel_id',
        'destination',
        'status',
        'attempts',
        'provider_name',
        'provider_message_id',
        'failure_reason',
        'queued_at',
        'sent_at',
        'delivered_at',
        'failed_at',
        'last_attempt_at',
        'provider_response',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'provider_response' => 'array',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(NotificationChannel::class, 'notification_channel_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }
}

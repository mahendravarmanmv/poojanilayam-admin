<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LivePoojaSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_pooja_id',
        'session_number',
        'status',
        'scheduled_start_at',
        'scheduled_end_at',
        'started_at',
        'ended_at',
        'minimum_video_quality',
        'noise_cancellation_enabled',
        'video_enhancement_enabled',
        'auto_lighting_enabled',
        'auto_recording_enabled',
        'host_controls_enabled',
        'chat_enabled',
        'screen_recording_protection_enabled',
    ];

    protected function casts(): array
    {
        return [
            'session_number' => 'integer',
            'scheduled_start_at' => 'datetime',
            'scheduled_end_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'noise_cancellation_enabled' => 'boolean',
            'video_enhancement_enabled' => 'boolean',
            'auto_lighting_enabled' => 'boolean',
            'auto_recording_enabled' => 'boolean',
            'host_controls_enabled' => 'boolean',
            'chat_enabled' => 'boolean',
            'screen_recording_protection_enabled' => 'boolean',
        ];
    }

    public function livePooja(): BelongsTo
    {
        return $this->belongsTo(LivePooja::class);
    }

    public function meeting(): HasOne
    {
        return $this->hasOne(LivePoojaMeeting::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LivePoojaParticipant::class);
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(LivePoojaRecording::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(LivePoojaReminder::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(BookingDeliverable::class);
    }
}

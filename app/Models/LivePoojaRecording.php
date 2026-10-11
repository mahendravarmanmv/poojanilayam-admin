<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePoojaRecording extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_pooja_session_id',
        'recording_id',
        'status',
        'storage_provider',
        'file_path',
        'file_url',
        'file_size_bytes',
        'duration_seconds',
        'resolution',
        'video_enhanced',
        'recorded_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'duration_seconds' => 'integer',
            'video_enhanced' => 'boolean',
            'recorded_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function livePoojaSession(): BelongsTo
    {
        return $this->belongsTo(LivePoojaSession::class);
    }
}

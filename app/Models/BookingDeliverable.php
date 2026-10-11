<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDeliverable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'uploaded_by_user_id',
        'live_pooja_session_id',
        'deliverable_id',
        'deliverable_type',
        'title',
        'description',
        'file_path',
        'file_url',
        'mime_type',
        'file_size_bytes',
        'status',
        'customer_visible',
        'visible_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'customer_visible' => 'boolean',
            'visible_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function livePoojaSession(): BelongsTo
    {
        return $this->belongsTo(LivePoojaSession::class);
    }
}

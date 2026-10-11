<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'review_id', 'customer_profile_id', 'reviewable_type', 'reviewable_id',
        'booking_id', 'order_id', 'rating', 'title', 'review_text', 'status',
        'is_verified', 'is_featured', 'customer_visible', 'submitted_at',
        'published_at', 'hidden_at', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer', 'is_verified' => 'boolean', 'is_featured' => 'boolean',
            'customer_visible' => 'boolean', 'submitted_at' => 'datetime',
            'published_at' => 'datetime', 'hidden_at' => 'datetime', 'metadata' => 'array',
        ];
    }

    public function customerProfile(): BelongsTo { return $this->belongsTo(CustomerProfile::class); }
    public function reviewable(): MorphTo { return $this->morphTo(); }
    public function booking(): BelongsTo { return $this->belongsTo(Booking::class); }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function ratings(): HasMany { return $this->hasMany(ReviewRating::class); }
    public function responses(): HasMany { return $this->hasMany(ReviewResponse::class); }
    public function media(): HasMany { return $this->hasMany(ReviewMedia::class); }
    public function statusHistories(): HasMany { return $this->hasMany(ReviewStatusHistory::class); }
}

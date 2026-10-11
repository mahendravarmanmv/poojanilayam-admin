<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReviewResponse extends Model
{
    use HasFactory;
    protected $fillable = ['review_id','user_id','response_text','customer_visible','responded_at'];
    protected function casts(): array { return ['customer_visible' => 'boolean','responded_at' => 'datetime']; }
    public function review(): BelongsTo { return $this->belongsTo(Review::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

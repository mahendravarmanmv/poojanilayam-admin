<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReviewRating extends Model
{
    use HasFactory;
    protected $fillable = ['review_id','rating_key','label','rating'];
    protected function casts(): array { return ['rating' => 'integer']; }
    public function review(): BelongsTo { return $this->belongsTo(Review::class); }
}

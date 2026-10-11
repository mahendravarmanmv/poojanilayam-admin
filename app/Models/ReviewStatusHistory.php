<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReviewStatusHistory extends Model
{
    use HasFactory;
    protected $fillable = ['review_id','from_status','to_status','changed_by_user_id','source','remarks','changed_at'];
    protected function casts(): array { return ['changed_at' => 'datetime']; }
    public function review(): BelongsTo { return $this->belongsTo(Review::class); }
    public function changedByUser(): BelongsTo { return $this->belongsTo(User::class,'changed_by_user_id'); }
}

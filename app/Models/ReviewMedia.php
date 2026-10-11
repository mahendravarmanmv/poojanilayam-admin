<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReviewMedia extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['review_id','uploaded_by_user_id','media_type','file_path','file_name','mime_type','file_size','sort_order','active'];
    protected function casts(): array { return ['file_size' => 'integer','sort_order' => 'integer','active' => 'boolean']; }
    public function review(): BelongsTo { return $this->belongsTo(Review::class); }
    public function uploadedByUser(): BelongsTo { return $this->belongsTo(User::class,'uploaded_by_user_id'); }
}

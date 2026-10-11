<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayoutStatusHistory extends Model
{
    use HasFactory;


    protected $fillable = ['payout_id','from_status','to_status','changed_by_user_id','source','remarks','changed_at'];
    protected $casts = ['changed_at'=>'datetime'];
    public function payout(): BelongsTo { return $this->belongsTo(Payout::class); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class,'changed_by_user_id'); }

}

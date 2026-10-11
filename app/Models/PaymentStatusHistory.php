<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentStatusHistory extends Model
{
    use HasFactory;


    protected $fillable = ['payment_id','from_status','to_status','changed_by_user_id','source','remarks','changed_at'];
    protected $casts = ['changed_at'=>'datetime'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function changedBy(): BelongsTo { return $this->belongsTo(User::class,'changed_by_user_id'); }

}

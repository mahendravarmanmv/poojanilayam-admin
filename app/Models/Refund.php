<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Refund extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['payment_id','refund_id','reference_id','status','requested_amount','approved_amount','refunded_amount','currency_code','currency_id','requested_by_user_id','reviewed_by_user_id','customer_reason','admin_remarks','rejection_reason','requested_at','reviewed_at','approved_at','processed_at','completed_at','rejected_at'];
    protected $casts = ['requested_amount'=>'decimal:2','approved_amount'=>'decimal:2','refunded_amount'=>'decimal:2','requested_at'=>'datetime','reviewed_at'=>'datetime','approved_at'=>'datetime','processed_at'=>'datetime','completed_at'=>'datetime','rejected_at'=>'datetime'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class,'requested_by_user_id'); }
    public function reviewedBy(): BelongsTo { return $this->belongsTo(User::class,'reviewed_by_user_id'); }
    public function items(): HasMany { return $this->hasMany(RefundItem::class); }
    public function statusHistories(): HasMany { return $this->hasMany(RefundStatusHistory::class); }
    public function transactions(): HasMany { return $this->hasMany(RefundTransaction::class); }
    public function gatewayCallbacks(): HasMany { return $this->hasMany(RefundGatewayCallback::class); }

}

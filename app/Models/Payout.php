<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payout extends Model
{
    use HasFactory;


    protected $fillable = ['payout_id','reference_id','settlement_id','payee_type','payee_id','bank_account_type','bank_account_id','currency_id','requested_amount','approved_amount','paid_amount','status','requested_by_user_id','approved_by_user_id','bank_account_name','bank_name','bank_account_last4','bank_ifsc','bank_reference','request_remarks','admin_remarks','failure_reason','requested_at','approved_at','processing_at','paid_at','failed_at','cancelled_at','metadata'];
    protected $casts = ['payee_id'=>'integer','bank_account_id'=>'integer','requested_amount'=>'decimal:2','approved_amount'=>'decimal:2','paid_amount'=>'decimal:2','requested_at'=>'datetime','approved_at'=>'datetime','processing_at'=>'datetime','paid_at'=>'datetime','failed_at'=>'datetime','cancelled_at'=>'datetime','metadata'=>'array'];
    public function settlement(): BelongsTo { return $this->belongsTo(Settlement::class); }
    public function payee(): MorphTo { return $this->morphTo(); }
    public function bankAccount(): MorphTo { return $this->morphTo(); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function requestedBy(): BelongsTo { return $this->belongsTo(User::class,'requested_by_user_id'); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class,'approved_by_user_id'); }
    public function transactions(): HasMany { return $this->hasMany(PayoutTransaction::class); }
    public function statusHistories(): HasMany { return $this->hasMany(PayoutStatusHistory::class); }

}

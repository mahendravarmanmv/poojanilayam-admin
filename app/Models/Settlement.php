<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Settlement extends Model
{
    use HasFactory;


    protected $fillable = ['settlement_id','reference_id','payment_id','settleable_type','settleable_id','beneficiary_type','beneficiary_id','settlement_type','currency_id','gross_amount','commission_amount','adjustment_amount','net_amount','status','eligible_at','calculated_at','approved_at','settled_at','failed_at','approved_by_user_id','remarks','metadata'];
    protected $casts = ['settleable_id'=>'integer','beneficiary_id'=>'integer','gross_amount'=>'decimal:2','commission_amount'=>'decimal:2','adjustment_amount'=>'decimal:2','net_amount'=>'decimal:2','eligible_at'=>'datetime','calculated_at'=>'datetime','approved_at'=>'datetime','settled_at'=>'datetime','failed_at'=>'datetime','metadata'=>'array'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function settleable(): MorphTo { return $this->morphTo(); }
    public function beneficiary(): MorphTo { return $this->morphTo(); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class,'approved_by_user_id'); }
    public function items(): HasMany { return $this->hasMany(SettlementItem::class); }
    public function commissions(): HasMany { return $this->hasMany(Commission::class); }
    public function payouts(): HasMany { return $this->hasMany(Payout::class); }

}

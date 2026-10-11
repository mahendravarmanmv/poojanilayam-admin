<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['payment_id','reference_id','payable_type','payable_id','customer_profile_id','payment_method_id','gateway_name','gateway_reference','currency_code','currency_id','amount','gateway_fee','platform_fee','net_amount','status','payment_context','initiated_at','authorized_at','paid_at','failed_at','cancelled_at','refunded_at','failure_reason','metadata'];
    protected $casts = ['payable_id'=>'integer','amount'=>'decimal:2','gateway_fee'=>'decimal:2','platform_fee'=>'decimal:2','net_amount'=>'decimal:2','initiated_at'=>'datetime','authorized_at'=>'datetime','paid_at'=>'datetime','failed_at'=>'datetime','cancelled_at'=>'datetime','refunded_at'=>'datetime','metadata'=>'array'];

    public function payable(): MorphTo { return $this->morphTo(); }
    public function customerProfile(): BelongsTo { return $this->belongsTo(CustomerProfile::class); }
    public function paymentMethod(): BelongsTo { return $this->belongsTo(PaymentMethod::class); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function transactions(): HasMany { return $this->hasMany(PaymentTransaction::class); }
    public function gatewayTransactions(): HasMany { return $this->hasMany(PaymentGatewayTransaction::class); }
    public function callbacks(): HasMany { return $this->hasMany(PaymentCallback::class); }
    public function statusHistories(): HasMany { return $this->hasMany(PaymentStatusHistory::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function refunds(): HasMany { return $this->hasMany(Refund::class); }
    public function settlements(): HasMany { return $this->hasMany(Settlement::class); }

}

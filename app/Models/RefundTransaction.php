<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RefundTransaction extends Model
{
    use HasFactory;


    protected $fillable = ['refund_id','transaction_id','transaction_type','status','amount','currency_code','gateway_name','gateway_refund_id','gateway_transaction_id','processed_at','failure_reason','metadata'];
    protected $casts = ['amount'=>'decimal:2','processed_at'=>'datetime','metadata'=>'array'];
    public function refund(): BelongsTo { return $this->belongsTo(Refund::class); }

}

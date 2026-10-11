<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PayoutTransaction extends Model
{
    use HasFactory;


    protected $fillable = ['payout_id','transaction_id','transaction_type','status','amount','currency_id','gateway_name','gateway_transaction_id','gateway_reference','failure_reason','processed_at','request_payload','response_payload'];
    protected $casts = ['amount'=>'decimal:2','processed_at'=>'datetime','request_payload'=>'array','response_payload'=>'array'];
    public function payout(): BelongsTo { return $this->belongsTo(Payout::class); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }

}

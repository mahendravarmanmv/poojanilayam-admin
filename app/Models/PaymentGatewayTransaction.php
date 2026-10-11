<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentGatewayTransaction extends Model
{
    use HasFactory;


    protected $fillable = ['payment_id','gateway_name','merchant_order_id','gateway_payment_id','gateway_transaction_id','gateway_status','gateway_response_code','gateway_amount','currency_code','request_payload','response_payload','initiated_at','completed_at'];
    protected $casts = ['gateway_amount'=>'decimal:2','request_payload'=>'array','response_payload'=>'array','initiated_at'=>'datetime','completed_at'=>'datetime'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }

}

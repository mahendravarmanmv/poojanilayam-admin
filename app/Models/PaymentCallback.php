<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentCallback extends Model
{
    use HasFactory;


    protected $fillable = ['payment_id','gateway_name','callback_event','callback_id','signature','signature_verified','processed','payload','processing_error','received_at','processed_at'];
    protected $casts = ['signature_verified'=>'boolean','processed'=>'boolean','payload'=>'array','received_at'=>'datetime','processed_at'=>'datetime'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RefundItem extends Model
{
    use HasFactory;


    protected $fillable = ['refund_id','item_type','description','invoice_item_id','quantity','amount','reason','metadata'];
    protected $casts = ['quantity'=>'integer','amount'=>'decimal:2','metadata'=>'array'];
    public function refund(): BelongsTo { return $this->belongsTo(Refund::class); }
    public function invoiceItem(): BelongsTo { return $this->belongsTo(InvoiceItem::class); }

}

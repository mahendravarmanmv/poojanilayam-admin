<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['payment_id','invoice_id','invoice_number','invoiceable_type','invoiceable_id','currency_code','currency_id','subtotal','discount_amount','tax_amount','total_amount','status','issued_at','due_at','file_path','file_url','billing_snapshot','metadata'];
    protected $casts = ['invoiceable_id'=>'integer','subtotal'=>'decimal:2','discount_amount'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2','issued_at'=>'datetime','due_at'=>'datetime','billing_snapshot'=>'array','metadata'=>'array'];
    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function invoiceable(): MorphTo { return $this->morphTo(); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function refunds(): HasMany { return $this->hasMany(Refund::class); }

}

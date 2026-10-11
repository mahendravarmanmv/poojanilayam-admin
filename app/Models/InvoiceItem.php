<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InvoiceItem extends Model
{
    use HasFactory;


    protected $fillable = ['invoice_id','item_type','description','quantity','unit_price','discount_amount','tax_percentage','tax_amount','line_total','metadata'];
    protected $casts = ['quantity'=>'integer','unit_price'=>'decimal:2','discount_amount'=>'decimal:2','tax_percentage'=>'decimal:3','tax_amount'=>'decimal:2','line_total'=>'decimal:2','metadata'=>'array'];
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function refundItems(): HasMany { return $this->hasMany(RefundItem::class); }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SettlementItem extends Model
{
    use HasFactory;


    protected $fillable = ['settlement_id','sourceable_type','sourceable_id','item_type','description','gross_amount','commission_amount','adjustment_amount','net_amount','currency_id','metadata'];
    protected $casts = ['sourceable_id'=>'integer','gross_amount'=>'decimal:2','commission_amount'=>'decimal:2','adjustment_amount'=>'decimal:2','net_amount'=>'decimal:2','metadata'=>'array'];
    public function settlement(): BelongsTo { return $this->belongsTo(Settlement::class); }
    public function sourceable(): MorphTo { return $this->morphTo(); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }

}

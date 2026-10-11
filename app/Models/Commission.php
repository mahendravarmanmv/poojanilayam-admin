<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Commission extends Model
{
    use HasFactory;


    protected $fillable = ['commission_id','settlement_id','commission_rule_id','sourceable_type','sourceable_id','beneficiary_type','beneficiary_id','commission_type','calculation_type','base_amount','percentage','fixed_amount','commission_amount','currency_id','status','calculated_at','metadata'];
    protected $casts = ['sourceable_id'=>'integer','beneficiary_id'=>'integer','base_amount'=>'decimal:2','percentage'=>'decimal:4','fixed_amount'=>'decimal:2','commission_amount'=>'decimal:2','calculated_at'=>'datetime','metadata'=>'array'];
    public function settlement(): BelongsTo { return $this->belongsTo(Settlement::class); }
    public function commissionRule(): BelongsTo { return $this->belongsTo(CommissionRule::class); }
    public function sourceable(): MorphTo { return $this->morphTo(); }
    public function beneficiary(): MorphTo { return $this->morphTo(); }
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }

}

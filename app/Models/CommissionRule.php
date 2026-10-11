<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommissionRule extends Model
{
    use HasFactory;


    protected $fillable = ['rule_code','name','applies_to_type','beneficiary_type','calculation_type','percentage','fixed_amount','currency_id','minimum_base_amount','maximum_base_amount','priority','conditions','effective_from','effective_until','active'];
    protected $casts = ['percentage'=>'decimal:4','fixed_amount'=>'decimal:2','minimum_base_amount'=>'decimal:2','maximum_base_amount'=>'decimal:2','priority'=>'integer','conditions'=>'array','effective_from'=>'datetime','effective_until'=>'datetime','active'=>'boolean'];
    public function currency(): BelongsTo { return $this->belongsTo(Currency::class); }
    public function commissions(): HasMany { return $this->hasMany(Commission::class); }

}

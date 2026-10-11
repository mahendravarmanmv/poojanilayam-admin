<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyServicePricing extends Model
{ use HasFactory; protected $table='astrology_service_pricing'; protected $fillable=['astrology_service_id','currency_id','pricing_type','amount','discount_amount','effective_from','effective_until','is_default','active']; protected $casts=['amount'=>'decimal:2','discount_amount'=>'decimal:2','effective_from'=>'datetime','effective_until'=>'datetime','is_default'=>'boolean','active'=>'boolean']; public function service(): BelongsTo { return $this->belongsTo(AstrologyService::class,'astrology_service_id'); } public function currency(): BelongsTo { return $this->belongsTo(Currency::class); } }

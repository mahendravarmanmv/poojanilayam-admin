<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerService extends Model
{ use HasFactory; protected $table='astrologer_services'; protected $fillable=['astrologer_profile_id','astrology_service_id','active','featured','sort_order']; protected $casts=['active'=>'boolean','featured'=>'boolean','sort_order'=>'integer']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } public function astrologyService(): BelongsTo { return $this->belongsTo(AstrologyService::class,'astrology_service_id'); } }

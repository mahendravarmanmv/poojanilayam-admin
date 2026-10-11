<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyService extends Model
{ use HasFactory, SoftDeletes; protected $table='astrology_services'; protected $fillable=['category_id','service_code','name','slug','service_type','short_description','description','duration_minutes','consultation_modes','image_path','banner_path','status','featured','active','sort_order']; protected $casts=['consultation_modes'=>'array','duration_minutes'=>'integer','featured'=>'boolean','active'=>'boolean','sort_order'=>'integer']; public function category(): BelongsTo { return $this->belongsTo(AstrologyServiceCategory::class,'category_id'); } public function pricing(): HasMany { return $this->hasMany(AstrologyServicePricing::class,'astrology_service_id'); } public function astrologerServices(): HasMany { return $this->hasMany(AstrologerService::class,'astrology_service_id'); } public function bookings(): HasMany { return $this->hasMany(AstrologyBooking::class,'astrology_service_id'); } }

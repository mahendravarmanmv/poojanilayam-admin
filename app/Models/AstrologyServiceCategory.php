<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyServiceCategory extends Model
{ use HasFactory, SoftDeletes; protected $table='astrology_service_categories'; protected $fillable=['category_code','name','slug','description','image_path','sort_order','active']; protected $casts=['active'=>'boolean','sort_order'=>'integer']; public function services(): HasMany { return $this->hasMany(AstrologyService::class,'category_id'); } }

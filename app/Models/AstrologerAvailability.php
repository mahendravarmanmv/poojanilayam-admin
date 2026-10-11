<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerAvailability extends Model
{ use HasFactory; protected $table='astrologer_availabilities'; protected $fillable=['astrologer_profile_id','availability_type','day_of_week','availability_date','start_time','end_time','timezone','is_available']; protected $casts=['day_of_week'=>'integer','availability_date'=>'date','is_available'=>'boolean']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } }

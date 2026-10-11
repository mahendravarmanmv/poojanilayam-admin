<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerExperience extends Model
{ use HasFactory; protected $table='astrologer_experiences'; protected $fillable=['astrologer_profile_id','organization_name','role_title','started_on','ended_on','is_current','description']; protected $casts=['started_on'=>'date','ended_on'=>'date','is_current'=>'boolean']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } }

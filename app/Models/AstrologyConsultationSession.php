<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyConsultationSession extends Model
{ use HasFactory; protected $table='astrology_consultation_sessions'; protected $fillable=['astrology_consultation_id','session_id','provider','session_type','meeting_url','access_token_reference','scheduled_at','started_at','ended_at','expires_at','status','metadata']; protected $casts=['scheduled_at'=>'datetime','started_at'=>'datetime','ended_at'=>'datetime','expires_at'=>'datetime','metadata'=>'array']; public function consultation(): BelongsTo { return $this->belongsTo(AstrologyConsultation::class,'astrology_consultation_id'); } }

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyConsultation extends Model
{ use HasFactory; protected $table='astrology_consultations'; protected $fillable=['astrology_booking_id','status','consultation_mode','customer_question','astrologer_notes','scheduled_at','started_at','ended_at','actual_duration_minutes']; protected $casts=['scheduled_at'=>'datetime','started_at'=>'datetime','ended_at'=>'datetime','actual_duration_minutes'=>'integer']; public function booking(): BelongsTo { return $this->belongsTo(AstrologyBooking::class,'astrology_booking_id'); } public function sessions(): HasMany { return $this->hasMany(AstrologyConsultationSession::class,'astrology_consultation_id'); } public function messages(): HasMany { return $this->hasMany(AstrologyConsultationMessage::class,'astrology_consultation_id'); } public function media(): HasMany { return $this->hasMany(AstrologyConsultationMedia::class,'astrology_consultation_id'); } public function reports(): HasMany { return $this->hasMany(AstrologyReport::class,'astrology_consultation_id'); } }

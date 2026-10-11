<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyConsultationMessage extends Model
{ use HasFactory, SoftDeletes; protected $table='astrology_consultation_messages'; protected $fillable=['astrology_consultation_id','sender_user_id','message_type','message','metadata','sent_at']; protected $casts=['metadata'=>'array','sent_at'=>'datetime']; public function consultation(): BelongsTo { return $this->belongsTo(AstrologyConsultation::class,'astrology_consultation_id'); } public function sender(): BelongsTo { return $this->belongsTo(User::class,'sender_user_id'); } }

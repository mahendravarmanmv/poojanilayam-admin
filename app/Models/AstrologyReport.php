<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyReport extends Model
{ use HasFactory, SoftDeletes; protected $table='astrology_reports'; protected $fillable=['astrology_booking_id','astrology_consultation_id','uploaded_by_user_id','report_id','report_type','title','description','file_path','report_url','original_name','mime_type','file_size','status','customer_visible','published_at','metadata']; protected $casts=['file_size'=>'integer','customer_visible'=>'boolean','published_at'=>'datetime','metadata'=>'array']; public function booking(): BelongsTo { return $this->belongsTo(AstrologyBooking::class,'astrology_booking_id'); } public function consultation(): BelongsTo { return $this->belongsTo(AstrologyConsultation::class,'astrology_consultation_id'); } public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class,'uploaded_by_user_id'); } }

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyConsultationMedia extends Model
{ use HasFactory, SoftDeletes; protected $table='astrology_consultation_media'; protected $fillable=['astrology_consultation_id','uploaded_by_user_id','media_type','file_path','media_url','original_name','mime_type','file_size','title','description','customer_visible','visible_at','metadata']; protected $casts=['file_size'=>'integer','customer_visible'=>'boolean','visible_at'=>'datetime','metadata'=>'array']; public function consultation(): BelongsTo { return $this->belongsTo(AstrologyConsultation::class,'astrology_consultation_id'); } public function uploadedBy(): BelongsTo { return $this->belongsTo(User::class,'uploaded_by_user_id'); } }

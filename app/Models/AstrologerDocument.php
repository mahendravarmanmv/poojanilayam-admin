<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerDocument extends Model
{ use HasFactory, SoftDeletes; protected $table='astrologer_documents'; protected $fillable=['astrologer_profile_id','document_type','document_number','file_path','original_name','mime_type','file_size','status','remarks','verified_at','verified_by_user_id']; protected $casts=['file_size'=>'integer','verified_at'=>'datetime']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } public function verifiedBy(): BelongsTo { return $this->belongsTo(User::class,'verified_by_user_id'); } }

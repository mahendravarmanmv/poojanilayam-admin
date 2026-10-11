<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerVerification extends Model
{ use HasFactory; protected $table='astrologer_verifications'; protected $fillable=['astrologer_profile_id','status','verified_by_user_id','verified_at','remarks']; protected $casts=['verified_at'=>'datetime']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } public function verifiedBy(): BelongsTo { return $this->belongsTo(User::class,'verified_by_user_id'); } }

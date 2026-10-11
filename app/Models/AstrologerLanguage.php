<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerLanguage extends Model
{ use HasFactory; protected $table='astrologer_languages'; protected $fillable=['astrologer_profile_id','language_id','proficiency','is_primary']; protected $casts=['is_primary'=>'boolean']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } public function language(): BelongsTo { return $this->belongsTo(Language::class); } }

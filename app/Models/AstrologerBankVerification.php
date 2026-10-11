<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerBankVerification extends Model
{ use HasFactory; protected $table='astrologer_bank_verifications'; protected $fillable=['astrologer_bank_account_id','status','verified_by_user_id','verified_at','remarks']; protected $casts=['verified_at'=>'datetime']; public function bankAccount(): BelongsTo { return $this->belongsTo(AstrologerBankAccount::class,'astrologer_bank_account_id'); } public function verifiedBy(): BelongsTo { return $this->belongsTo(User::class,'verified_by_user_id'); } }

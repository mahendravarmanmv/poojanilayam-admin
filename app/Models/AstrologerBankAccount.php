<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologerBankAccount extends Model
{ use HasFactory; protected $table='astrologer_bank_accounts'; protected $fillable=['astrologer_profile_id','account_holder_name','bank_name','branch_name','account_number','masked_account_number','ifsc_code','account_type','upi_id','status','is_primary','verified_at']; protected $casts=['is_primary'=>'boolean','verified_at'=>'datetime']; public function astrologerProfile(): BelongsTo { return $this->belongsTo(AstrologerProfile::class); } public function verifications(): HasMany { return $this->hasMany(AstrologerBankVerification::class); } }

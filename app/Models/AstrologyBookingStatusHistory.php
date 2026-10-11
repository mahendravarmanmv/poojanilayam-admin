<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AstrologyBookingStatusHistory extends Model
{ use HasFactory; protected $table='astrology_booking_status_histories'; protected $fillable=['astrology_booking_id','from_status','to_status','changed_by_user_id','source','remarks','changed_at']; protected $casts=['changed_at'=>'datetime']; public function booking(): BelongsTo { return $this->belongsTo(AstrologyBooking::class,'astrology_booking_id'); } public function changedBy(): BelongsTo { return $this->belongsTo(User::class,'changed_by_user_id'); } }

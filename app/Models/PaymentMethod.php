<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = ['method_code','name','method_type','provider_name','configuration','supports_refund','active','sort_order'];
    protected $casts = ['configuration'=>'array','supports_refund'=>'boolean','active'=>'boolean','sort_order'=>'integer'];

    public function payments(): HasMany { return $this->hasMany(Payment::class); }

}

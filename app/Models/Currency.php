<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'symbol', 'decimal_places', 'is_active'];

    protected function casts(): array
    {
        return ['decimal_places' => 'integer', 'is_active' => 'boolean'];
    }

    public function countries(): HasMany { return $this->hasMany(Country::class); }
    public function productPrices(): HasMany { return $this->hasMany(ProductPrice::class); }
    public function bookings(): HasMany { return $this->hasMany(Booking::class); }
    public function astrologyBookings(): HasMany { return $this->hasMany(AstrologyBooking::class); }
    public function donations(): HasMany { return $this->hasMany(Donation::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function payouts(): HasMany { return $this->hasMany(Payout::class); }
    public function payoutTransactions(): HasMany { return $this->hasMany(PayoutTransaction::class); }
    public function refunds(): HasMany { return $this->hasMany(Refund::class); }
    public function commissions(): HasMany { return $this->hasMany(Commission::class); }
    public function commissionRules(): HasMany { return $this->hasMany(CommissionRule::class); }
    public function settlements(): HasMany { return $this->hasMany(Settlement::class); }
    public function settlementItems(): HasMany { return $this->hasMany(SettlementItem::class); }
    public function eventRegistrations(): HasMany { return $this->hasMany(EventRegistration::class); }
}

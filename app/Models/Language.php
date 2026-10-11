<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'native_name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function userProfiles(): HasMany { return $this->hasMany(UserProfile::class, 'preferred_language_id'); }
    public function customerProfiles(): HasMany { return $this->hasMany(CustomerProfile::class, 'preferred_language_id'); }
    public function userPreferences(): HasMany { return $this->hasMany(UserPreference::class); }
    public function notificationTemplates(): HasMany { return $this->hasMany(NotificationTemplate::class); }
    public function astrologerLanguages(): HasMany { return $this->hasMany(AstrologerLanguage::class); }
    public function pujariLanguages(): HasMany { return $this->hasMany(PujariLanguage::class); }
    public function bookingSankalpams(): HasMany { return $this->hasMany(BookingSankalpam::class); }
    public function digitalPoojaSelections(): HasMany { return $this->hasMany(DigitalPoojaSelection::class); }
    public function digitalPoojaSankalpams(): HasMany { return $this->hasMany(DigitalPoojaSankalpam::class); }
    public function mantraTranslations(): HasMany { return $this->hasMany(MantraTranslation::class); }
    public function mantraMedia(): HasMany { return $this->hasMany(MantraMedia::class); }
}

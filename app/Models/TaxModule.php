<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxModule extends Model
{
    protected $table = 'tax_module';

    protected $fillable = [
        'tax_id',
        'module',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
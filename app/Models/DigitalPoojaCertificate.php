<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalPoojaCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'digital_pooja_id',
        'certificate_number',
        'title',
        'file_path',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }

    public function digitalPooja(): BelongsTo
    {
        return $this->belongsTo(DigitalPooja::class);
    }
}

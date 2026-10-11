<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsRedirect extends Model
{
    use HasFactory;

    protected $table = 'cms_redirects';

    protected $fillable = ['source_path', 'destination_path', 'status_code', 'active'];

    protected function casts(): array
    {
        return ['status_code' => 'integer', 'active' => 'boolean'];
    }
}

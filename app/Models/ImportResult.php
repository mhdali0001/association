<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportResult extends Model
{
    protected $fillable = [
        'user_id', 'status',
        'file_path', 'file_ext', 'total_rows', 'processed_rows',
        'imported', 'skipped', 'errors',
    ];

    protected $casts = [
        'imported' => 'array',
        'skipped'  => 'array',
        'errors'   => 'array',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkRevertItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'session_id', 'member_id', 'member_snapshot', 'scores_snapshot',
    ];

    protected $casts = [
        'member_snapshot' => 'array',
        'scores_snapshot' => 'array',
        'created_at'      => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(BulkRevertSession::class, 'session_id');
    }
}

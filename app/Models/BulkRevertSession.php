<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkRevertSession extends Model
{
    protected $fillable = [
        'user_id', 'operation', 'description', 'affected_count',
        'reverted_at', 'reverted_by',
    ];

    protected $casts = [
        'reverted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revertedByUser()
    {
        return $this->belongsTo(User::class, 'reverted_by');
    }

    public function items()
    {
        return $this->hasMany(BulkRevertItem::class, 'session_id');
    }

    public function isReverted(): bool
    {
        return $this->reverted_at !== null;
    }
}

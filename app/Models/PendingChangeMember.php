<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingChangeMember extends Model
{
    protected $fillable = [
        'pending_change_id', 'member_id', 'full_name', 'dossier_number', 'before', 'after',
    ];

    protected $casts = [
        'before' => 'array',
        'after'  => 'array',
    ];

    public function pendingChange()
    {
        return $this->belongsTo(PendingChange::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReview extends Model
{
    protected $fillable = ['member_id', 'status', 'notes', 'reviewed_by', 'reviewed_at'];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isMatch(): bool   { return $this->status === 'match';   }
    public function isMismatch(): bool{ return $this->status === 'mismatch';}
}

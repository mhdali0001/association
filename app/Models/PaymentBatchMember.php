<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBatchMember extends Model
{
    protected $fillable = [
        'batch_id', 'member_id', 'previous_count', 'new_count', 'estimated_amount',
    ];

    public function batch()
    {
        return $this->belongsTo(PaymentBatch::class, 'batch_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}

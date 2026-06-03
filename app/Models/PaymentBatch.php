<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBatch extends Model
{
    protected $fillable = [
        'label', 'payment_date', 'operation', 'amount', 'members_count',
        'total_estimated_amount', 'notes', 'applied_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function members()
    {
        return $this->hasMany(PaymentBatchMember::class, 'batch_id');
    }

    public function appliedBy()
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function getOperationLabelAttribute(): string
    {
        return match($this->operation) {
            'add'      => 'إضافة',
            'subtract' => 'طرح',
            default    => 'تعيين',
        };
    }
}

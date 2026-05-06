<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTransaction extends Model
{
    protected $fillable = ['employee_id', 'type', 'amount', 'currency', 'reason', 'transaction_date', 'created_by'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match($this->type) {
            'salary'    => 'راتب',
            'addition'  => 'إضافة',
            'deduction' => 'خصم',
            'advance'   => 'سلفة',
            'bonus'     => 'مكافأة',
            default     => $this->type,
        };
    }

    public function currencySymbol(): string
    {
        return $this->currency === 'USD' ? '$' : 'ل.س';
    }

    public function isCredit(): bool
    {
        return in_array($this->type, ['salary', 'addition', 'advance', 'bonus']);
    }
}

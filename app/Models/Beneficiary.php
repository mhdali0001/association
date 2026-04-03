<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = ['user_id', 'name', 'allocated_amount', 'notes'];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function totalPaid(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function remaining(): float
    {
        return (float) $this->allocated_amount - $this->totalPaid();
    }

    public function statusLabel(): string
    {
        $paid  = $this->totalPaid();
        $alloc = (float) $this->allocated_amount;

        if ($alloc <= 0)           return 'غير محدد';
        if ($paid <= 0)            return 'لم يتم الدفع';
        if ($paid >= $alloc)       return 'تم الدفع بالكامل';
        return 'تم الدفع جزئياً';
    }

    public function statusColor(): string
    {
        return match ($this->statusLabel()) {
            'تم الدفع بالكامل'  => 'emerald',
            'تم الدفع جزئياً'  => 'amber',
            'لم يتم الدفع'     => 'red',
            default             => 'gray',
        };
    }
}

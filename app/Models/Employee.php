<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = ['name', 'job_title', 'phone', 'base_salary', 'base_salary_currency', 'notes', 'is_active'];

    protected $casts = [
        'is_active'   => 'boolean',
        'base_salary' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(EmployeeTransaction::class)->orderByDesc('transaction_date')->orderByDesc('id');
    }

    public function netBalance(?string $currency = null): float
    {
        $tx = $this->relationLoaded('transactions')
            ? $this->transactions
            : $this->transactions()->get();

        if ($currency !== null) {
            $tx = $tx->where('currency', $currency);
        }

        $credits = $tx->whereIn('type', ['salary', 'addition', 'advance'])->sum('amount');
        $debits  = $tx->where('type', 'deduction')->sum('amount');

        return (float) ($credits - $debits);
    }

    public function hasCurrency(string $currency): bool
    {
        $tx = $this->relationLoaded('transactions')
            ? $this->transactions
            : $this->transactions()->get();

        return $tx->where('currency', $currency)->isNotEmpty();
    }
}

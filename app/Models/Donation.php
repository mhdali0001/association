<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'member_id',
        'amount',
        'donation_month',
        'type',
        'status',
        'reference_number',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'donation_month' => 'date',
        'amount'         => 'decimal:2',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('donation_month', $year)
                     ->whereMonth('donation_month', $month);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Helpers ──────────────────────────────────────────────
    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'manual'    => 'يدوي',
            'sham_cash' => 'شام كاش',
            default     => $type,
        };
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'paid'      => 'مدفوع',
            'pending'   => 'معلّق',
            'cancelled' => 'ملغي',
            default     => $status,
        };
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            'paid'      => 'emerald',
            'pending'   => 'yellow',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }
}

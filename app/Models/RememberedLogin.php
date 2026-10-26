<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class RememberedLogin extends Model
{
    use Prunable;

    protected $fillable = ['user_id', 'token_hash', 'user_agent', 'last_used_at', 'expires_at'];

    protected $hidden = ['token_hash'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * حذف الحسابات المحفوظة المنتهية (php artisan model:prune).
     */
    public function prunable(): Builder
    {
        return static::where('expires_at', '<', now());
    }
}

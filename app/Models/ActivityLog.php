<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id',
        'subject_label', 'description', 'properties',
        'ip_address', 'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Action label helpers ──────────────────────────────────────

    public function actionLabel(): string
    {
        return match($this->action) {
            'login'   => 'تسجيل دخول',
            'logout'  => 'تسجيل خروج',
            'created' => 'إضافة',
            'updated' => 'تعديل',
            'deleted' => 'حذف',
            'viewed'  => 'عرض',
            default   => $this->action,
        };
    }

    public function actionColor(): string
    {
        return match($this->action) {
            'login'   => 'emerald',
            'logout'  => 'gray',
            'created' => 'blue',
            'updated' => 'yellow',
            'deleted' => 'red',
            'viewed'  => 'purple',
            default   => 'gray',
        };
    }
}

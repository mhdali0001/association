<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(
        string  $action,
        string  $description,
        ?Model  $subject   = null,
        ?array  $properties = null,
        ?int    $userId    = null
    ): void {
        ActivityLog::create([
            'user_id'       => $userId ?? Auth::id(),
            'action'        => $action,
            'subject_type'  => $subject ? class_basename($subject) : null,
            'subject_id'    => $subject?->getKey(),
            'subject_label' => self::subjectLabel($subject),
            'description'   => $description,
            'properties'    => $properties,
            'ip_address'    => Request::ip(),
            'user_agent'    => Request::userAgent(),
        ]);
    }

    private static function subjectLabel(?Model $subject): ?string
    {
        if (!$subject) return null;

        return $subject->full_name
            ?? $subject->name
            ?? $subject->title
            ?? ('#' . $subject->getKey());
    }
}

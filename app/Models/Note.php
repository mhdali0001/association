<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /** التصنيفات المتاحة: المفتاح يُخزَّن، والقيمة تسمية ولون. */
    public const CATEGORIES = [
        'visit'     => ['label' => 'زيارة ميدانية', 'color' => 'blue'],
        'call'      => ['label' => 'مكالمة هاتفية', 'color' => 'indigo'],
        'aid'       => ['label' => 'تسليم مساعدة',  'color' => 'emerald'],
        'complaint' => ['label' => 'شكوى',          'color' => 'red'],
        'decision'  => ['label' => 'قرار',          'color' => 'violet'],
        'follow_up' => ['label' => 'متابعة',        'color' => 'amber'],
        'other'     => ['label' => 'أخرى',          'color' => 'gray'],
    ];

    protected $fillable = [
        'title', 'category', 'body', 'note_date', 'pinned', 'created_by',
    ];

    protected $casts = [
        'note_date' => 'date',
        'pinned'    => 'boolean',
    ];

    protected static function booted(): void
    {
        // Remove attached files when a note is deleted (cascade delete on the
        // pivot/attachments FK does not fire model events, so do it here).
        static::deleting(function (Note $note) {
            $note->attachments()->get()->each->delete();
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'note_member');
    }

    public function attachments()
    {
        return $this->hasMany(NoteAttachment::class)->latest();
    }

    /* ── Category helpers ─────────────────────────────────────────── */

    public static function categoryLabelFor(?string $key): string
    {
        return self::CATEGORIES[$key]['label'] ?? 'بدون تصنيف';
    }

    public function categoryLabel(): string
    {
        return self::categoryLabelFor($this->category);
    }

    public function categoryColor(): string
    {
        return self::CATEGORIES[$this->category]['color'] ?? 'gray';
    }

    /* ── Relative Arabic date ─────────────────────────────────────── */

    public function relativeDate(): string
    {
        $date = $this->note_date ?? $this->created_at;
        if (! $date) {
            return '—';
        }

        $date  = $date->copy()->startOfDay();
        $today = now()->startOfDay();

        if ($date->greaterThan($today)) {
            return $date->format('Y/m/d');
        }

        $days = (int) $date->diffInDays($today);

        return match (true) {
            $days === 0 => 'اليوم',
            $days === 1 => 'أمس',
            $days === 2 => 'قبل يومين',
            $days <= 10 => "قبل {$days} أيام",
            $days <= 13 => 'قبل أسبوع',
            $days <= 20 => 'قبل أسبوعين',
            $days <= 27 => 'قبل 3 أسابيع',
            default     => $date->format('Y/m/d'),
        };
    }

    public function exactDate(): string
    {
        return optional($this->note_date)->format('Y/m/d') ?? $this->created_at->format('Y/m/d');
    }
}

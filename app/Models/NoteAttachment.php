<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NoteAttachment extends Model
{
    protected $fillable = [
        'note_id', 'file_path', 'file_name', 'file_size', 'mime_type', 'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (NoteAttachment $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        });
    }

    public function note()
    {
        return $this->belongsTo(Note::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }
}

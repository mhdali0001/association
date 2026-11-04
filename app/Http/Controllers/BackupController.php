<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Config\Config as BackupConfig;

/**
 * نسخة احتياطية يدوية لقاعدة البيانات إلى Google Drive بضغطة زر —
 * إضافة إلى النسخة الأسبوعية التلقائية (routes/console.php)، وبنفس إعدادات config/backup.php (كلمة سر الأرشيف…).
 */
class BackupController extends Controller
{
    private const DISK = 'google';
    private const LOCK = 'manual-database-backup';

    public function index()
    {
        // آخر النسخ الموجودة فعلاً على Drive — تأكيد أن النسخة وصلت
        try {
            $backups = BackupDestination::create(self::DISK, config('backup.backup.name'))->backups()->take(10);
            $error   = null;
        } catch (\Throwable $e) {
            $backups = collect();
            $error   = $e->getMessage();
        }

        return view('backups.index', compact('backups', 'error'));
    }

    public function store()
    {
        // ضغطة ثانية أثناء الإرسال لا تبدأ نسخة أخرى
        $lock = Cache::lock(self::LOCK, 15 * 60);
        if (! $lock->get()) {
            return back()->with('error', 'نسخة احتياطية قيد الإرسال الآن — انتظر حتى تنتهي ثم حدّث الصفحة.');
        }

        try {
            @set_time_limit(15 * 60);
            ignore_user_abort(true); // إغلاق الصفحة أثناء الإرسال لا يقطع النسخة

            // الزر يرسل إلى Google Drive دائماً، مهما كانت وجهات النسخة التلقائية في BACKUP_DISKS.
            // إعدادات spatie تُقرأ مرة لكل طلب (scoped)، فنُسقط النسخة المقروءة لتُبنى من الإعداد الجديد.
            config(['backup.backup.destination.disks' => [self::DISK]]);
            app()->forgetInstance(BackupConfig::class);

            $exitCode = Artisan::call('backup:run', ['--only-db' => true, '--only-to-disk' => self::DISK]);
            $output   = Artisan::output();
        } catch (\Throwable $e) {
            $exitCode = 1;
            $output   = $e->getMessage();
        } finally {
            $lock->release();
        }

        if ($exitCode !== 0) {
            Log::error('Manual database backup to Google Drive failed', ['output' => $output]);

            return back()->with('error', 'فشل إرسال النسخة الاحتياطية: ' . $this->failureReason($output));
        }

        ActivityLogger::log('backup', 'نسخة احتياطية يدوية لقاعدة البيانات إلى Google Drive');

        return back()->with('success', 'تم إرسال نسخة احتياطية من قاعدة البيانات إلى Google Drive.');
    }

    /** السبب الحقيقي في «Error Output»، لا في رسالة spatie العامة «The dump process failed…» */
    private function failureReason(string $output): string
    {
        if (preg_match('/mysqldump\W*\s+is not recognized|mysqldump: (command )?not found/i', $output)) {
            return 'أداة mysqldump غير موجودة على هذا الخادم — حدّد مجلدها في DB_DUMP_BINARY_PATH في ملف .env.';
        }
        if (preg_match('/Error Output\s*=+\s*(\S[^\n]*)/i', $output, $m)) {
            return mb_substr(trim($m[1]), 0, 300);
        }
        if (preg_match('/failed because:?\s*(.+)/i', $output, $m)) {
            return mb_substr(trim($m[1]), 0, 300);
        }
        $lines = array_values(array_filter(array_map('trim', explode("\n", $output))));

        return mb_substr(end($lines) ?: 'سبب غير معروف — راجع سجل النظام', 0, 300);
    }
}

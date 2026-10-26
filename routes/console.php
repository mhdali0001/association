<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| النسخ الاحتياطي اليومي (spatie/laravel-backup)
|--------------------------------------------------------------------------
| يتطلب كرون واحد على الخادم:
|   * * * * * /opt/alt/php83/usr/bin/php /home/USER/public_html/artisan schedule:run >/dev/null 2>&1
*/
// فحص ربط Google Drive: يرفع ملفاً تجريبياً ثم يحذفه، ويطبع سبب الفشل الحقيقي إن وُجد.
Artisan::command('backup:test-google', function () {
    try {
        $disk = Storage::disk('google');
        $file = 'connection-test-' . now()->format('Y-m-d-H-i-s') . '.txt';
        $disk->getDriver()->write($file, 'masalkalnour backup connection test');
        $this->info('✔ تم الرفع: ' . $file);
        $disk->getDriver()->delete($file);
        $this->info('✔ تم الحذف — الربط مع Google Drive يعمل.');
    } catch (\Throwable $e) {
        $this->error('✘ فشل الربط مع Google Drive: ' . $e->getMessage());
        return 1;
    }
})->purpose('Test the Google Drive backup destination');

Schedule::command('backup:clean')->daily()->at('01:30')->withoutOverlapping();
Schedule::command('backup:run')->daily()->at('02:00')->withoutOverlapping()->runInBackground();
Schedule::command('backup:monitor')->daily()->at('06:00');

// حذف الحسابات المحفوظة المنتهية من الأجهزة
Schedule::command('model:prune', ['--model' => [\App\Models\RememberedLogin::class]])->daily()->at('03:00');

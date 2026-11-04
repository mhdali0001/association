<?php

namespace App\Providers;

use App\Services\RememberedLogins;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Spatie\Backup\Events\BackupZipWasCreated;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // الحسابات المحفوظة على هذا الجهاز — لقائمة "تبديل الحساب" في الشريط العلوي
        View::composer('layouts.app', function ($view) {
            $view->with('rememberedAccounts', RememberedLogins::forDevice(request()));
        });

        // قرص Google Drive — وجهة للنسخ الاحتياطي عبر BACKUP_DISKS=backups,google
        Storage::extend('google', function ($app, array $config) {
            if (empty($config['clientId']) || empty($config['clientSecret']) || empty($config['refreshToken'])) {
                throw new \RuntimeException('Google Drive غير مضبوط: أضف GOOGLE_DRIVE_CLIENT_ID و GOOGLE_DRIVE_CLIENT_SECRET و GOOGLE_DRIVE_REFRESH_TOKEN إلى .env');
            }

            $client = new GoogleClient();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);

            $token = $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
            if (isset($token['error'])) {
                // invalid_grant غالباً = انتهى الربط (تطبيق OAuth في وضع Testing) أو أُلغي — يلزم Refresh Token جديد
                throw new \RuntimeException('Google Drive رفض الربط: ' . ($token['error_description'] ?? $token['error']));
            }

            $adapter = new GoogleDriveAdapter(new GoogleDrive($client), $config['folder'] ?? '/');

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        // زر «إرسال نسخة احتياطية الآن» متاح لكل المستخدمين: 5 نسخ في الساعة لكل مستخدم، وبعدها رسالة لا صفحة 429
        RateLimiter::for('manual-backups', fn (Request $request) => Limit::perHour(5)
            ->by($request->user()?->id ?: $request->ip())
            ->response(fn () => back()->with('error', 'وصلت إلى حد 5 نسخ احتياطية في الساعة — حاول بعد قليل.')));

        // Google Drive يرفض عرض مجلد غير موجود («File not found») بدل إرجاع قائمة فارغة، فيفشل فحص
        // الوجهة قبل الرفع («Could not connect to disk google») ما دام مجلد النسخ لم يُنشأ بعد.
        // ننشئه بعد تجهيز الأرشيف وقبل رفعه — يشمل النسخة اليومية وزر صفحة النسخ الاحتياطي.
        Event::listen(BackupZipWasCreated::class, function () {
            $folder = config('backup.backup.name');
            foreach (config('backup.backup.destination.disks', []) as $diskName) {
                if (config("filesystems.disks.{$diskName}.driver") !== 'google') {
                    continue;
                }
                $disk = Storage::disk($diskName);
                if (! $disk->directoryExists($folder)) {
                    $disk->makeDirectory($folder);
                }
            }
        });
    }
}

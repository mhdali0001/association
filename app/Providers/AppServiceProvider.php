<?php

namespace App\Providers;

use App\Services\RememberedLogins;
use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDrive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

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
    }
}

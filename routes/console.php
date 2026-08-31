<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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
Schedule::command('backup:clean')->daily()->at('01:30')->withoutOverlapping();
Schedule::command('backup:run')->daily()->at('02:00')->withoutOverlapping()->runInBackground();
Schedule::command('backup:monitor')->daily()->at('06:00');

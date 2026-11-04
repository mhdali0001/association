<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the tables this page touches.
beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('role')->default('user');
        $table->rememberToken();
        $table->timestamps();
    });
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable();
        $table->string('action');
        $table->string('subject_type')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->string('subject_label')->nullable();
        $table->text('description');
        $table->json('properties')->nullable();
        $table->string('ip_address', 45)->nullable();
        $table->string('user_agent')->nullable();
        $table->timestamps();
    });

    // the main layout counts pending requests for the sidebar badge
    Schema::create('pending_changes', function (Blueprint $table) {
        $table->id();
        $table->string('model_type');
        $table->unsignedBigInteger('model_id')->nullable();
        $table->string('action');
        $table->longText('payload')->nullable();
        $table->foreignId('requested_by')->nullable();
        $table->string('status')->default('pending');
        $table->timestamps();
    });

    // Google Drive → a local fake disk: nothing leaves the test
    Storage::fake('google');
    $this->admin = User::factory()->create(['role' => 'admin']);
});

it('is for admins only', function () {
    $this->get('/backups')->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get('/backups')->assertForbidden();
    $this->actingAs(User::factory()->create())->post('/backups')->assertForbidden();
});

it('lists the latest backups that are on Google Drive', function () {
    Storage::disk('google')->put('masalkalnour/2026-11-04-02-00-00.zip', str_repeat('x', 3 * 1024 * 1024));
    Storage::disk('google')->put('masalkalnour/2026-11-03-02-00-00.zip', str_repeat('x', 500 * 1024));

    $this->actingAs($this->admin)->get('/backups')->assertOk()
        ->assertSee('إرسال نسخة احتياطية الآن إلى Google Drive')
        ->assertSeeInOrder(['2026/11/04 — 02:00', '3.0 MB', '2026/11/03 — 02:00', '500 KB']);
});

it('sends a database-only backup to Google Drive, whatever the daily destinations are', function () {
    config(['backup.backup.destination.disks' => ['backups']]);
    Artisan::shouldReceive('call')->once()
        ->with('backup:run', ['--only-db' => true, '--only-to-disk' => 'google'])
        ->andReturnUsing(function () {
            expect(config('backup.backup.destination.disks'))->toBe(['google']);
            return 0;
        });
    Artisan::shouldReceive('output')->andReturn('Backup completed!');

    $this->actingAs($this->admin)->from('/backups')->post('/backups')
        ->assertRedirect('/backups')
        ->assertSessionHas('success', 'تم إرسال نسخة احتياطية من قاعدة البيانات إلى Google Drive.');

    expect(\App\Models\ActivityLog::sole()->action)->toBe('backup')
        ->and(Cache::lock('manual-database-backup')->get())->toBeTrue(); // released for the next press
});

it('shows the real reason when the backup fails', function (string $output, string $expected) {
    Artisan::shouldReceive('call')->once()->andReturn(1);
    Artisan::shouldReceive('output')->andReturn($output);

    $this->actingAs($this->admin)->from('/backups')->post('/backups')
        ->assertSessionHas('error', fn ($msg) => str_contains($msg, $expected));
})->with([
    'mysqldump missing (windows)' => ["Backup failed because: The dump process failed with a non-successful exit code.\nError Output\n============\n'\"mysqldump\"' is not recognized as an internal or external command,", 'أداة mysqldump غير موجودة'],
    'mysqldump missing (linux)'   => ["Backup failed because: The dump process failed.\nError Output\n============\nsh: 1: mysqldump: not found", 'أداة mysqldump غير موجودة'],
    'database error'              => ["Backup failed because: The dump process failed.\nError Output\n============\nmysqldump: Got error: 1045: Access denied for user", 'Access denied'],
    'drive error'                 => ['Backup failed because: Google Drive رفض الربط: invalid_grant.', 'invalid_grant'],
]);

it('does not start a second backup while one is being sent', function () {
    Cache::lock('manual-database-backup', 60)->get();
    Artisan::shouldReceive('call')->never();

    $this->actingAs($this->admin)->from('/backups')->post('/backups')
        ->assertSessionHas('error', fn ($msg) => str_contains($msg, 'قيد الإرسال'));
});

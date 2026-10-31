<?php

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the tables login touches.
beforeEach(function () {
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('role')->default('user');
        $table->string('phone')->nullable();
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
    (require database_path('migrations/2026_10_27_000001_create_remembered_logins_table.php'))->up();

    // Laravel skips the CSRF check while testing — turn it back on so these tests hit the real 419
    $this->app['env'] = 'local';
    Log::spy();
});

const EXPIRED = 'انتهت صلاحية الصفحة، أعد المحاولة.';

it('still logs in with a valid token', function () {
    $user = User::factory()->create();
    $this->get('/login');

    $this->post('/login', ['_token' => session()->token(), 'email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('sends an expired login form back to a fresh login page instead of 419', function () {
    $user = User::factory()->create();

    $this->post('/login', ['_token' => 'stale', 'email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors(['email' => EXPIRED])
        ->assertSessionHasInput('email', $user->email);

    $this->get('/login')->assertOk()->assertSee(EXPIRED);
    Log::shouldHaveReceived('info')->withArgs(fn ($msg, $ctx) => str_contains($msg, '419') && $ctx['path'] === 'login');
});

it('goes straight to the dashboard when an earlier login attempt already succeeded', function () {
    $this->actingAs(User::factory()->create());

    $this->post('/login', ['_token' => 'stale'])->assertRedirect(route('dashboard'));
});

it('shows the saved accounts again with a message when a saved-account button expired', function () {
    $this->post('/login/remembered/1', ['_token' => 'stale'])
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', EXPIRED);
});

it('returns other expired forms to the previous page with a message', function () {
    $this->actingAs(User::factory()->create());

    $this->from('/dashboard')->post('/logout', ['_token' => 'stale'])
        ->assertRedirect('/dashboard')
        ->assertSessionHas('error', EXPIRED);
});

it('keeps answering 419 to JSON requests', function () {
    $this->actingAs(User::factory()->create());

    $this->postJson('/members-duplicates/similar-names/dismiss', ['a' => [1], 'b' => [2]])->assertStatus(419);
});

it('tells the browser not to keep the login page, so "back" fetches a fresh token', function () {
    expect($this->get('/login')->headers->get('Cache-Control'))->toContain('no-store');
});

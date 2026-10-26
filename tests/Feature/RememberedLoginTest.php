<?php

use App\Models\RememberedLogin;
use App\Models\User;
use App\Services\RememberedLogins;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the tables this feature touches:
// minimal users + activity_logs, and remembered_logins from its real migration.
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
});

/** Device cookie value (decrypted) set by a response, or null. */
function deviceCookie($response): ?string
{
    return $response->getCookie(RememberedLogins::COOKIE)?->getValue();
}

function savedId(User $user): int
{
    return RememberedLogin::where('user_id', $user->id)->value('id');
}

it('saves the account on this device when "remember" is checked', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']);

    $response->assertRedirect(route('dashboard'));
    $entries = json_decode(deviceCookie($response), true);
    expect($entries)->toHaveCount(1)
        ->and(RememberedLogin::sole()->token_hash)->toBe(hash('sha256', reset($entries)));
});

it('does not save the account when "remember" is unchecked', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

    $response->assertRedirect(route('dashboard'));
    expect(deviceCookie($response))->toBeNull()
        ->and(RememberedLogin::count())->toBe(0);
});

it('shows the saved accounts on the login page', function () {
    $user = User::factory()->create(['name' => 'سارة علي']);
    $cookie = deviceCookie($this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']));
    $this->post('/logout');

    $this->withCookie(RememberedLogins::COOKIE, $cookie)->get('/login')
        ->assertOk()
        ->assertSee('اختر حسابك للمتابعة')
        ->assertSee('سارة علي')
        ->assertSee($user->email);
});

it('logs in with one click and rotates the token', function () {
    $user = User::factory()->create();
    $cookie = deviceCookie($this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']));
    $this->post('/logout');
    $this->assertGuest();

    $response = $this->withCookie(RememberedLogins::COOKIE, $cookie)->post('/login/remembered/' . savedId($user));

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
    expect(deviceCookie($response))->not->toBe($cookie);

    // The previous token no longer works after rotation
    $this->post('/logout');
    $this->withCookie(RememberedLogins::COOKIE, $cookie)->post('/login/remembered/' . savedId($user))
        ->assertRedirect(route('login', ['add' => 1]));
    $this->assertGuest();
});

it('switches between two saved accounts without a password', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $cookieA = deviceCookie($this->post('/login', ['email' => $a->email, 'password' => 'password', 'remember' => '1']));
    $this->post('/logout');
    $cookieAB = deviceCookie($this->withCookie(RememberedLogins::COOKIE, $cookieA)
        ->post('/login', ['email' => $b->email, 'password' => 'password', 'remember' => '1']));
    $this->assertAuthenticatedAs($b);
    expect(json_decode($cookieAB, true))->toHaveCount(2);

    $this->withCookie(RememberedLogins::COOKIE, $cookieAB)->post('/login/remembered/' . savedId($a))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($a);
});

it('removes an account from this device', function () {
    $user = User::factory()->create();
    $cookie = deviceCookie($this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']));
    $this->post('/logout');

    $response = $this->withCookie(RememberedLogins::COOKIE, $cookie)->delete('/login/remembered/' . savedId($user));

    $response->assertCookieExpired(RememberedLogins::COOKIE);
    expect(RememberedLogin::count())->toBe(0);
});

it('cancels saved accounts when the password changes', function () {
    $user = User::factory()->create();
    $cookie = deviceCookie($this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']));
    $id = savedId($user);
    $this->post('/logout');

    $user->update(['password' => 'a-new-password-123']);

    expect(RememberedLogin::count())->toBe(0);
    $this->withCookie(RememberedLogins::COOKIE, $cookie)->post("/login/remembered/{$id}")
        ->assertRedirect(route('login', ['add' => 1]));
    $this->assertGuest();
});

it('rejects a wrong token without deleting the saved account', function () {
    $user = User::factory()->create();
    $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']);
    $id = savedId($user);
    $this->post('/logout');

    $this->withCookie(RememberedLogins::COOKIE, json_encode([$id => 'not-the-token']))->post("/login/remembered/{$id}")
        ->assertRedirect(route('login', ['add' => 1]));

    $this->assertGuest();
    expect(RememberedLogin::count())->toBe(1);
});

it('opens the password form directly after "add another account"', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post('/logout', ['add_account' => '1'])
        ->assertRedirect(route('login', ['add' => 1]));
});

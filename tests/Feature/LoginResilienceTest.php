<?php

use App\Models\User;
use App\Services\RememberedLogins;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A server where the remembered_logins migration has not run: only users + activity_logs exist.
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
});

it('logs in even when saving the account on this device fails', function () {
    $user = User::factory()->create();

    // «تذكرني على هذا الجهاز» is ticked by default
    $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1'])
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('still shows the login page when the saved accounts cannot be read', function () {
    $this->withCookie(RememberedLogins::COOKIE, json_encode(['7' => str_repeat('a', 64)]))
        ->get('/login')
        ->assertOk()
        ->assertSee('سجّل دخولك');
});

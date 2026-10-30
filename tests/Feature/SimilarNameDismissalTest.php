<?php

use App\Models\Member;
use App\Models\SimilarNameDismissal;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the tables this feature touches:
// minimal users + members + activity_logs, and similar_name_dismissals from its real migration.
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
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
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
    (require database_path('migrations/2026_10_30_000002_create_similar_name_dismissals_table.php'))->up();

    $this->actingAs(User::factory()->create());
});

it('dismisses every pair between the two spellings and logs it', function () {
    [$a1, $a2, $b] = collect(['سمر برغش', 'سمر برغش', 'سمر احمد برغش'])->map(fn($n) => Member::create(['full_name' => $n]));

    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$a1->id, $a2->id], 'b' => [$b->id]])
        ->assertOk();

    expect(SimilarNameDismissal::pluck('member_b_id', 'member_a_id')->all())->toBe([$a1->id => $b->id, $a2->id => $b->id])
        ->and(SimilarNameDismissal::first()->user_id)->toBe(auth()->id())
        ->and(\App\Models\ActivityLog::sole()->action)->toBe('reviewed');
});

it('stores the pair in a fixed order and ignores repeated clicks', function () {
    [$a, $b] = collect(['ناهد دباس', 'ناهدة دباس'])->map(fn($n) => Member::create(['full_name' => $n]));

    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$b->id], 'b' => [$a->id]])->assertOk();
    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$a->id], 'b' => [$b->id]])->assertOk();

    expect(SimilarNameDismissal::count())->toBe(1)
        ->and(SimilarNameDismissal::first()->member_a_id)->toBe($a->id);
});

it('restores a dismissed group', function () {
    [$a, $b] = collect(['ناهد دباس', 'ناهدة دباس'])->map(fn($n) => Member::create(['full_name' => $n]));
    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$a->id], 'b' => [$b->id]]);

    $this->deleteJson(route('members.duplicates.similar-names.restore'), ['a' => [$a->id], 'b' => [$b->id]])
        ->assertOk();

    expect(SimilarNameDismissal::count())->toBe(0);
});

it('rejects unknown members and a member on both sides', function () {
    $a = Member::create(['full_name' => 'ناهد دباس']);

    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$a->id], 'b' => [999]])
        ->assertUnprocessable();
    $this->postJson(route('members.duplicates.similar-names.dismiss'), ['a' => [$a->id], 'b' => [$a->id]])
        ->assertStatus(422);

    expect(SimilarNameDismissal::count())->toBe(0);
});

it('falls back to a redirect for a plain form post', function () {
    [$a, $b] = collect(['ناهد دباس', 'ناهدة دباس'])->map(fn($n) => Member::create(['full_name' => $n]));

    $this->from(route('members.duplicates.similar-names'))
        ->post(route('members.duplicates.similar-names.dismiss'), ['a' => [$a->id], 'b' => [$b->id]])
        ->assertRedirect(route('members.duplicates.similar-names'))
        ->assertSessionHas('success');
});

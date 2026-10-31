<?php

use App\Http\Controllers\MemberController;
use App\Models\Member;
use App\Models\PendingChange;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the columns these checks touch.
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
        $table->string('national_id')->nullable();
        $table->string('dossier_number')->nullable()->unique();
        $table->timestamps();
    });
    Schema::create('pending_changes', function (Blueprint $table) {
        $table->id();
        $table->string('model_type');
        $table->unsignedBigInteger('model_id')->nullable();
        $table->string('action');
        $table->longText('payload')->nullable();
        $table->longText('original')->nullable();
        $table->foreignId('requested_by')->nullable();
        $table->string('status')->default('pending');
        $table->foreignId('reviewed_by')->nullable();
        $table->timestamp('reviewed_at')->nullable();
        $table->text('reviewer_notes')->nullable();
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

    Member::create(['full_name' => 'عبد العزيز المصري', 'national_id' => '01010000001', 'dossier_number' => '1939']);
    $this->actingAs(User::factory()->create(['role' => 'admin']));
});

function createRequest(array $payload): PendingChange
{
    return PendingChange::create(['model_type' => 'member', 'action' => 'create', 'payload' => $payload, 'requested_by' => auth()->id()]);
}

it('explains a taken dossier number instead of showing the database error, and leaves the request pending', function () {
    $change = createRequest(['full_name' => 'ريم حسين ازكاحي', 'national_id' => '05110075595', 'dossier_number' => '1939']);

    $this->from(route('pending-changes.index'))
        ->post(route('pending-changes.bulk-approve'), ['ids' => [$change->id]])
        ->assertSessionHas('error', fn ($msg) => str_contains($msg, 'رقم الإضبارة 1939 مستخدم مسبقاً للعضو «عبد العزيز المصري»')
            && !str_contains($msg, 'SQLSTATE'));

    expect($change->fresh()->status)->toBe('pending')
        ->and(Member::count())->toBe(1);
});

it('refuses a national id that another member already has', function () {
    $change = createRequest(['full_name' => 'ريم حسين ازكاحي', 'national_id' => '01010000001', 'dossier_number' => '1940']);

    expect(fn () => $change->apply())->toThrow(RuntimeException::class, 'رقم الهوية 01010000001 مستخدم مسبقاً');
    expect($change->fresh()->status)->toBe('pending');
});

it('does not block an edit that keeps an old duplicate national id unchanged', function () {
    $member = Member::create(['full_name' => 'نسخة قديمة', 'national_id' => '01010000001', 'dossier_number' => '50']);
    $change = PendingChange::create(['model_type' => 'member', 'model_id' => $member->id, 'action' => 'update',
        'payload' => ['full_name' => 'نسخة قديمة', 'national_id' => '01010000001', 'dossier_number' => '50']]);

    $check = new ReflectionMethod(PendingChange::class, 'ensureUniqueMemberFields');
    expect(fn () => $check->invoke($change, ['national_id' => '01010000001', 'dossier_number' => '50']))->not->toThrow(RuntimeException::class);
});

it('rejects a new member whose dossier number is held by a pending add request', function () {
    createRequest(['full_name' => 'طلب سابق', 'dossier_number' => '1950']);
    $rule = (new ReflectionMethod(MemberController::class, 'notReservedByPendingRequest'))->invoke(app(MemberController::class));

    $validator = Validator::make(['dossier_number' => '1950'], ['dossier_number' => [$rule]]);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('dossier_number'))->toContain('محجوز في طلب إضافة قيد المراجعة')
        ->and(Validator::make(['dossier_number' => '1951'], ['dossier_number' => [$rule]])->fails())->toBeFalse();
});

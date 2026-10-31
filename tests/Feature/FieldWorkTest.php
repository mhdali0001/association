<?php

use App\Models\FieldVisit;
use App\Models\Member;
use App\Models\PendingChange;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the columns field work touches.
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
    foreach (['sectors', 'field_visit_statuses', 'house_types', 'house_conditions'] as $name) {
        Schema::create($name, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    Schema::create('regions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->boolean('is_active')->default(true);
        $table->foreignId('sector_id')->nullable();
        $table->timestamps();
    });
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('national_id')->nullable();
        $table->string('dossier_number')->nullable();
        $table->string('phone')->nullable();
        $table->string('phone2')->nullable();
        $table->text('current_address')->nullable();
        $table->foreignId('region_id')->nullable();
        $table->foreignId('sector_id')->nullable();
        $table->timestamps();
    });
    Schema::create('field_visits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id');
        $table->foreignId('created_by')->nullable();
        $table->foreignId('field_visit_status_id')->nullable();
        $table->foreignId('house_type_id')->nullable();
        $table->foreignId('house_condition_id')->nullable();
        $table->date('visit_date')->nullable();
        $table->string('visitor')->nullable();
        $table->decimal('estimated_amount', 12, 2)->nullable();
        $table->text('amount_reason')->nullable();
        $table->text('notes')->nullable();
        $table->boolean('has_video')->default(false);
        $table->boolean('has_special_case')->default(false);
        $table->timestamps();
    });
    (require database_path('migrations/2026_10_31_000001_add_client_id_to_field_visits_table.php'))->up();
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

    $sector = DB::table('sectors')->insertGetId(['name' => 'غوطة شرقية']);
    $other  = DB::table('sectors')->insertGetId(['name' => 'وسط دمشق']);
    $region = DB::table('regions')->insertGetId(['name' => 'حزه', 'sector_id' => $sector]);
    DB::table('field_visit_statuses')->insert(['name' => 'تمت الزيارة']);

    $this->visited = Member::create(['full_name' => 'فايزة غنوم', 'national_id' => '01010285800', 'dossier_number' => '2110',
        'phone' => '0952446820', 'current_address' => 'حزة', 'region_id' => $region, 'sector_id' => $sector]);
    FieldVisit::create(['member_id' => $this->visited->id, 'visit_date' => '2026-09-12', 'field_visit_status_id' => 1]);
    $this->fresh = Member::create(['full_name' => 'ايمان شيخ خليل', 'dossier_number' => '2200', 'region_id' => $region, 'sector_id' => $sector]);
    Member::create(['full_name' => 'عضو في قطاع آخر', 'dossier_number' => '5', 'sector_id' => $other]);
});

function visitPayload(int $memberId, array $extra = []): array
{
    return array_merge(['client_id' => (string) Str::uuid(), 'member_id' => $memberId, 'visit_date' => '2026-10-31', 'visitor' => 'هناء'], $extra);
}

it('serves a page with no beneficiary data in it, so it is safe to keep offline', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/field-work')->assertOk()->assertSee('العمل الميداني')
        ->assertDontSee('فايزة غنوم')->assertDontSee('0952446820');
});

it('downloads only the visit fields — no national id', function () {
    $this->actingAs(User::factory()->create());

    $pkg = $this->getJson('/field-work/package?sector_id=1')->assertOk()->json();

    expect($pkg['members'])->toHaveCount(2)
        ->and(array_keys($pkg['members'][0]))->toEqualCanonicalizing(['id', 'name', 'dossier', 'phone', 'phone2', 'address', 'region', 'visits', 'last_visit', 'last_status'])
        ->and($pkg['members'][0])->toMatchArray(['name' => 'فايزة غنوم', 'region' => 'حزه', 'visits' => 1, 'last_visit' => '2026-09-12', 'last_status' => 'تمت الزيارة'])
        ->and(json_encode($pkg))->not->toContain('01010285800')
        ->and($pkg['expires_at'] - $pkg['generated_at'])->toBe(24 * 3600 * 1000)
        ->and($pkg['options']['statuses'])->toHaveCount(1);
});

it('can download only members who were never visited', function () {
    $this->actingAs(User::factory()->create());

    expect($this->getJson('/field-work/package?not_visited=1&sector_id=1')->json('members.*.name'))->toBe(['ايمان شيخ خليل']);
});

it('tells the page to log in again instead of redirecting', function () {
    $this->getJson('/field-work/session')->assertUnauthorized();
    $this->getJson('/field-work/package')->assertUnauthorized();
});

it('records an admin visit sent from the device, once, even if it is sent twice', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $payload = visitPayload($this->fresh->id, ['estimated_amount' => 500, 'amount_operation' => 'subtract', 'has_video' => true]);

    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'created']);
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);

    $visit = FieldVisit::where('member_id', $this->fresh->id)->sole();
    expect($visit->client_id)->toBe($payload['client_id'])
        ->and((float) $visit->estimated_amount)->toBe(-500.0)
        ->and($visit->has_video)->toBeTrue()
        ->and($visit->created_by)->toBe(auth()->id());
});

it('sends a non-admin visit for approval, and keeps it single after approval', function () {
    $this->actingAs(User::factory()->create());
    $payload = visitPayload($this->fresh->id);

    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'pending']);
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);
    expect(PendingChange::count())->toBe(1);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
    PendingChange::sole()->apply();
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);

    expect(FieldVisit::where('member_id', $this->fresh->id)->sole()->client_id)->toBe($payload['client_id']);
});

it('explains why a stored visit cannot be sent', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->postJson('/field-work/visits', visitPayload(999))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['member_id' => 'هذا المستفيد لم يعد موجوداً في النظام.']);
    $this->postJson('/field-work/visits', visitPayload($this->fresh->id, ['client_id' => 'not-a-uuid']))
        ->assertJsonValidationErrors('client_id');
});

it('keeps the member page visit form working as before', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $this->post(route('field-visits.store', $this->fresh), ['visitor' => 'سمير', 'estimated_amount' => 300])
        ->assertRedirect(route('members.show', $this->fresh))->assertSessionHas('success');
    expect(FieldVisit::where('member_id', $this->fresh->id)->sole())
        ->visit_date->toDateString()->toBe(now()->toDateString())
        ->client_id->toBeNull();

    $this->actingAs(User::factory()->create());
    $this->post(route('field-visits.store', $this->visited), ['visitor' => 'سمير'])
        ->assertSessionHas('pending');
    expect(PendingChange::sole()->payload)->toMatchArray(['member_id' => $this->visited->id, 'member_name' => 'فايزة غنوم'])
        ->not->toHaveKey('client_id');
});

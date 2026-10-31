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
    foreach (['field_visit_statuses', 'house_types', 'house_conditions'] as $name) {
        Schema::create($name, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    Schema::create('members', function (Blueprint $table) {
        $table->id();
        $table->string('full_name');
        $table->string('national_id')->nullable();
        $table->string('dossier_number')->nullable()->unique();
        $table->string('phone')->nullable();
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

    DB::table('field_visit_statuses')->insert([['name' => 'تمت الزيارة', 'is_active' => true], ['name' => 'مخفي', 'is_active' => false]]);
    DB::table('house_types')->insert(['name' => 'بيت عربي']);
    DB::table('house_conditions')->insert(['name' => 'يحتاج اصلاحات شديدة']);
    $this->member = Member::create(['full_name' => 'ايمان شيخ خليل', 'national_id' => '01010221107', 'dossier_number' => '2200', 'phone' => '0962814542']);
});

function visitPayload(array $extra = []): array
{
    return array_merge(['client_id' => (string) Str::uuid(), 'dossier_number' => '2200', 'visit_date' => '2026-10-31', 'visitor' => 'هناء'], $extra);
}

it('shows the visit fields with their options, and no beneficiary data, so it is safe to keep offline', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/field-work')->assertOk()
        ->assertSeeInOrder(['رقم الإضبارة', 'الحالة', 'نوع البيت', 'تاريخ الزيارة', 'اسم الزائر', 'المبلغ', 'سبب المبلغ', 'ملاحظات', 'حالة البيت', 'يوجد فيديو', 'حالة خاصة'])
        ->assertSee('تمت الزيارة')->assertSee('بيت عربي')->assertSee('يحتاج اصلاحات شديدة')
        ->assertDontSee('مخفي')
        ->assertDontSee('ايمان شيخ خليل')->assertDontSee('0962814542');
});

it('shows the beneficiary name for a dossier number', function () {
    $this->actingAs(User::factory()->create());

    $this->getJson('/field-work/member?dossier=2200')->assertOk()->assertExactJson(['name' => 'ايمان شيخ خليل']);
    $this->getJson('/field-work/member?dossier=%202200%20')->assertOk();
    $this->getJson('/field-work/member?dossier=9999')->assertNotFound();
});

it('tells the page to log in again instead of redirecting', function () {
    $this->getJson('/field-work/session')->assertUnauthorized();
    $this->getJson('/field-work/member?dossier=2200')->assertUnauthorized();
});

it('records an admin visit by dossier number, once, even if it is sent twice', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $payload = visitPayload(['estimated_amount' => 500, 'amount_operation' => 'subtract', 'has_video' => true, 'field_visit_status_id' => 1]);

    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'created', 'member' => 'ايمان شيخ خليل']);
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);

    $visit = FieldVisit::where('member_id', $this->member->id)->sole();
    expect($visit->client_id)->toBe($payload['client_id'])
        ->and((float) $visit->estimated_amount)->toBe(-500.0)
        ->and($visit->has_video)->toBeTrue()
        ->and($visit->field_visit_status_id)->toBe(1)
        ->and($visit->created_by)->toBe(auth()->id());
});

it('sends a non-admin visit for approval, and keeps it single after approval', function () {
    $this->actingAs(User::factory()->create());
    $payload = visitPayload();

    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'pending']);
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);
    expect(PendingChange::count())->toBe(1);

    $this->actingAs(User::factory()->create(['role' => 'admin']));
    PendingChange::sole()->apply();
    $this->postJson('/field-work/visits', $payload)->assertOk()->assertJson(['status' => 'duplicate']);

    expect(FieldVisit::where('member_id', $this->member->id)->sole()->client_id)->toBe($payload['client_id']);
});

it('explains why a stored visit cannot be sent', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->postJson('/field-work/visits', visitPayload(['dossier_number' => '9999']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['dossier_number' => 'لا يوجد مستفيد برقم الإضبارة هذا.']);
    $this->postJson('/field-work/visits', visitPayload(['dossier_number' => null]))
        ->assertJsonValidationErrors(['dossier_number' => 'رقم الإضبارة مطلوب.']);
    $this->postJson('/field-work/visits', visitPayload(['client_id' => 'not-a-uuid']))
        ->assertJsonValidationErrors('client_id');
});

it('still accepts visits saved on a phone by the previous version (member id)', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));

    $this->postJson('/field-work/visits', visitPayload(['dossier_number' => null, 'member_id' => $this->member->id]))
        ->assertOk()->assertJson(['status' => 'created']);
});

it('keeps the member page visit form working as before', function () {
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $this->post(route('field-visits.store', $this->member), ['visitor' => 'سمير', 'estimated_amount' => 300])
        ->assertRedirect(route('members.show', $this->member))->assertSessionHas('success');
    expect(FieldVisit::where('member_id', $this->member->id)->sole())
        ->visit_date->toDateString()->toBe(now()->toDateString())
        ->client_id->toBeNull();

    $this->actingAs(User::factory()->create());
    $this->post(route('field-visits.store', $this->member), ['visitor' => 'سمير'])->assertSessionHas('pending');
    expect(PendingChange::sole()->payload)->toMatchArray(['member_id' => $this->member->id, 'member_name' => 'ايمان شيخ خليل'])
        ->not->toHaveKey('client_id');
});

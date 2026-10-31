<?php

use App\Models\FieldVisit;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Some older migrations use MySQL-only SQL, so the in-memory SQLite test DB gets just the columns the filters touch.
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
        $table->unsignedBigInteger('final_status_id')->nullable();
        $table->boolean('special_cases')->nullable();
        $table->timestamps();
    });
    Schema::create('field_visits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('member_id');
        $table->boolean('has_video')->nullable();
        $table->boolean('has_special_case')->nullable();
        $table->timestamps();
    });

    $this->actingAs(User::factory()->create());

    $withVideo = Member::create(['full_name' => 'زارتها جولة', 'final_status_id' => 1]);
    FieldVisit::forceCreate(['member_id' => $withVideo->id, 'has_video' => true, 'has_special_case' => false]);
    Member::create(['full_name' => 'بلا جولة', 'final_status_id' => 1]);
    Member::create(['full_name' => 'حالة نهائية أخرى', 'final_status_id' => 2]);
});

// The filter form always submits these selects, empty when not chosen.
const EMPTY_FORM = 'fv_has_video=&fv_has_special_case=&special_cases=&fv_notes=&fv_count=';

function exportCount(string $query): int
{
    return test()->getJson('/members/custom-export/count?' . $query)->assertOk()->json('count');
}

it('ignores empty yes/no selects instead of keeping only members with a field visit', function () {
    expect(exportCount('final_status_id[]=1&' . EMPTY_FORM))->toBe(2)
        ->and(exportCount(EMPTY_FORM))->toBe(3);
});

it('still filters when a yes/no select is chosen', function () {
    expect(exportCount('final_status_id[]=1&fv_has_video=1'))->toBe(1)
        ->and(exportCount('final_status_id[]=1&fv_has_video=0'))->toBe(0)
        ->and(exportCount('final_status_id[]=1&fv_has_special_case=0'))->toBe(1);
});

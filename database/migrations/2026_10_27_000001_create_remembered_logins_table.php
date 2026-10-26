<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * الحسابات المحفوظة على الأجهزة — دخول بضغطة واحدة من صفحة الدخول.
     * الرمز نفسه في كوكي مشفّرة على الجهاز، وهنا بصمته فقط (sha256).
     */
    public function up(): void
    {
        Schema::create('remembered_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->string('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remembered_logins');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('associations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('member_associations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('association_id')->constrained('associations')->cascadeOnDelete();
            $table->unique(['member_id', 'association_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_associations');
        Schema::dropIfExists('associations');
    }
};

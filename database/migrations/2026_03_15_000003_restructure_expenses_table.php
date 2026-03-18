<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Rename old columns
            $table->renameColumn('expense_type', 'title');
            $table->renameColumn('expense_date', 'date');
            $table->renameColumn('notes', 'description');

            // Drop unused column
            $table->dropForeign(['beneficiary_id']);
            $table->dropColumn('beneficiary_id');

            // Add missing columns
            $table->string('category')->nullable()->after('title');
            $table->string('recipient')->nullable()->after('description');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('title', 'expense_type');
            $table->renameColumn('date', 'expense_date');
            $table->renameColumn('description', 'notes');
            $table->dropColumn(['category', 'recipient', 'updated_at']);
            $table->foreignId('beneficiary_id')->nullable()->constrained('members')->nullOnDelete();
        });
    }
};

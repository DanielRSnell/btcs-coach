<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->text('goal')->nullable()->after('description');
            $table->json('sample_questions')->nullable()->after('topics');
            $table->text('expected_outcomes')->nullable()->after('learning_objectives');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['goal', 'sample_questions', 'expected_outcomes']);
        });
    }
};

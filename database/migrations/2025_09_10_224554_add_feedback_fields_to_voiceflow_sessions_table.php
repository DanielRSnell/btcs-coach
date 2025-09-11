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
        Schema::table('voiceflow_sessions', function (Blueprint $table) {
            $table->enum('feedback_rating', ['positive', 'negative'])->nullable()->after('session_updated_at');
            $table->text('feedback_comment')->nullable()->after('feedback_rating');
            $table->timestamp('feedback_submitted_at')->nullable()->after('feedback_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voiceflow_sessions', function (Blueprint $table) {
            $table->dropColumn(['feedback_rating', 'feedback_comment', 'feedback_submitted_at']);
        });
    }
};

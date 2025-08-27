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
            // Add project_id to store the Voiceflow project ID (the localStorage key)
            $table->string('project_id')->nullable()->after('session_id');
            
            // Add index for project_id
            $table->index(['project_id']);
        });
        
        // Update the unique constraint in a separate statement
        Schema::table('voiceflow_sessions', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'session_id']);
            $table->unique(['user_id', 'session_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voiceflow_sessions', function (Blueprint $table) {
            // Remove the new constraints and column
            $table->dropUnique(['user_id', 'session_id', 'project_id']);
            $table->dropIndex(['project_id']);
            $table->dropColumn('project_id');
            
            // Restore the original unique constraint
            $table->unique(['user_id', 'session_id']);
        });
    }
};

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
        Schema::create('voiceflow_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->index(); // The Voiceflow session ID
            $table->json('value_data')->nullable(); // The localStorage value data
            $table->string('status')->default('ACTIVE')->index(); // ACTIVE, INACTIVE, etc.
            $table->string('source')->default('unknown')->index(); // localStorage_sync, localStorage_change, etc.
            $table->timestamp('session_created_at')->nullable(); // When session was created in Voiceflow
            $table->timestamp('session_updated_at')->nullable(); // When session was last updated in Voiceflow
            $table->timestamps(); // Laravel created_at/updated_at for when row was created/modified
            
            // Indexes for better performance
            $table->unique(['user_id', 'session_id']); // One session per user
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['session_updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voiceflow_sessions');
    }
};
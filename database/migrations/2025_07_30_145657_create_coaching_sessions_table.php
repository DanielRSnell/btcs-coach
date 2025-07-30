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
        Schema::create('coaching_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique(); // Voiceflow session ID
            $table->string('topic')->nullable();
            $table->text('summary')->nullable();
            $table->json('voiceflow_data')->nullable(); // Store Voiceflow conversation data
            $table->integer('duration')->nullable(); // Duration in minutes
            $table->integer('interactions')->default(0); // Number of interactions
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->decimal('satisfaction_score', 3, 2)->nullable(); // 0.00 to 5.00
            $table->string('department')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaching_sessions');
    }
};

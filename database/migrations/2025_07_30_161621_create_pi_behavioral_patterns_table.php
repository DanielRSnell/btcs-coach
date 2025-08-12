<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pi_behavioral_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20);
            $table->text('description');
            $table->json('behavioral_drives'); // A, B, C, D scores
            $table->text('strengths');
            $table->text('challenges');
            $table->text('work_style');
            $table->text('communication_style');
            $table->text('leadership_style')->nullable();
            $table->text('ideal_work_environment');
            $table->text('motivation_factors');
            $table->text('stress_factors');
            $table->json('compatible_patterns')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('code');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pi_behavioral_patterns');
    }
};
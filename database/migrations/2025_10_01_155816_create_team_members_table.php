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
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number')->unique();
            $table->string('employee_name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('employee_email')->unique();
            $table->string('job');
            $table->string('job_code')->nullable();
            $table->string('org_level_2')->nullable();
            $table->string('employment_status');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('org_level_2');
            $table->index('employment_status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};

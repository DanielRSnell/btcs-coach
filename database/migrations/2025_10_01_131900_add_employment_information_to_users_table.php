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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->after('email');
            $table->string('org_level_2')->nullable()->after('employee_number');
            $table->string('job')->nullable()->after('org_level_2');
            $table->string('job_code')->nullable()->after('job');
            $table->string('employment_status')->nullable()->after('job_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_number',
                'org_level_2',
                'job',
                'job_code',
                'employment_status',
            ]);
        });
    }
};

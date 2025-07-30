<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pi_behavioral_pattern_id')->nullable()->constrained()->after('role');
            $table->json('pi_raw_scores')->nullable()->after('pi_behavioral_pattern_id'); // Individual A, B, C, D scores
            $table->timestamp('pi_assessed_at')->nullable()->after('pi_raw_scores');
            $table->string('pi_assessor_name')->nullable()->after('pi_assessed_at');
            $table->text('pi_notes')->nullable()->after('pi_assessor_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pi_behavioral_pattern_id']);
            $table->dropColumn([
                'pi_behavioral_pattern_id',
                'pi_raw_scores',
                'pi_assessed_at',
                'pi_assessor_name',
                'pi_notes'
            ]);
        });
    }
};
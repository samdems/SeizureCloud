<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seizures', function (Blueprint $table) {
            // Seizure type
            $table->enum('seizure_type', [
                'focal_aware',
                'focal_impaired',
                'focal_motor',
                'focal_non_motor',
                'generalized_tonic_clonic',
                'absence',
                'myoclonic',
                'atonic',
                'tonic',
                'clonic',
                'unknown'
            ])->nullable()->after('severity');

            // Video evidence
            $table->boolean('has_video_evidence')->default(false)->after('seizure_type');
            $table->text('video_notes')->nullable()->after('has_video_evidence');

            // Triggers (stored as JSON array)
            $table->json('triggers')->nullable()->after('video_notes');
            $table->text('other_triggers')->nullable()->after('triggers');

            // Pre-ictal symptoms
            $table->json('pre_ictal_symptoms')->nullable()->after('other_triggers');
            $table->text('pre_ictal_notes')->nullable()->after('pre_ictal_symptoms');

            // Post-ictal recovery
            $table->enum('recovery_time', [
                'immediate',
                'short',
                'moderate',
                'long',
                'very_long'
            ])->nullable()->after('postictal_state_end');
            $table->boolean('post_ictal_confusion')->default(false)->after('recovery_time');
            $table->boolean('post_ictal_headache')->default(false)->after('post_ictal_confusion');
            $table->text('recovery_notes')->nullable()->after('post_ictal_headache');

            // Period tracking
            $table->integer('days_since_period')->nullable()->after('on_period');

            // Medication adherence
            $table->enum('medication_adherence', [
                'excellent',
                'good',
                'fair',
                'poor'
            ])->nullable()->after('days_since_period');
            $table->boolean('recent_medication_change')->default(false)->after('medication_adherence');
            $table->boolean('experiencing_side_effects')->default(false)->after('recent_medication_change');
            $table->text('medication_notes')->nullable()->after('experiencing_side_effects');

            // General wellbeing
            $table->enum('wellbeing_rating', [
                'excellent',
                'good',
                'fair',
                'poor',
                'very_poor'
            ])->nullable()->after('medication_notes');
            $table->enum('sleep_quality', [
                'excellent',
                'good',
                'fair',
                'poor',
                'very_poor'
            ])->nullable()->after('wellbeing_rating');
            $table->text('wellbeing_notes')->nullable()->after('sleep_quality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seizures', function (Blueprint $table) {
            $table->dropColumn([
                'seizure_type',
                'has_video_evidence',
                'video_notes',
                'triggers',
                'other_triggers',
                'pre_ictal_symptoms',
                'pre_ictal_notes',
                'recovery_time',
                'post_ictal_confusion',
                'post_ictal_headache',
                'recovery_notes',
                'days_since_period',
                'medication_adherence',
                'recent_medication_change',
                'experiencing_side_effects',
                'medication_notes',
                'wellbeing_rating',
                'sleep_quality',
                'wellbeing_notes'
            ]);
        });
    }
};

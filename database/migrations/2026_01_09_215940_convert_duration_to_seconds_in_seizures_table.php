<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, convert existing duration_minutes to seconds
        DB::statement(
            "UPDATE seizures SET duration_minutes = duration_minutes * 60 WHERE duration_minutes IS NOT NULL",
        );

        // Then rename the column to duration_seconds
        Schema::table("seizures", function (Blueprint $table) {
            $table->renameColumn("duration_minutes", "duration_seconds");
        });

        // Note: Comments are not supported in SQLite, so we skip this for cross-database compatibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First rename back to duration_minutes
        Schema::table("seizures", function (Blueprint $table) {
            $table->renameColumn("duration_seconds", "duration_minutes");
        });

        // Then convert seconds back to minutes (rounded to nearest minute)
        DB::statement(
            "UPDATE seizures SET duration_minutes = ROUND(duration_minutes / 60) WHERE duration_minutes IS NOT NULL",
        );

        // Note: Comments are not supported in SQLite, so we skip this for cross-database compatibility
    }
};

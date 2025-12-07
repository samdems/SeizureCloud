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
        Schema::table("medication_schedules", function (Blueprint $table) {
            $table->dropColumn(["dosage", "unit"]);
            $table
                ->decimal("dosage_multiplier", 5, 2)
                ->default(1.0)
                ->after("scheduled_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("medication_schedules", function (Blueprint $table) {
            $table->dropColumn("dosage_multiplier");
            $table->string("dosage")->nullable()->after("scheduled_time");
            $table->string("unit")->nullable()->after("dosage");
        });
    }
};

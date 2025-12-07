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
        Schema::table("users", function (Blueprint $table) {
            $table->integer("status_epilepticus_duration_minutes")->default(5);
            $table->integer("emergency_seizure_count")->default(3);
            $table->text("emergency_contact_info")->nullable();
            $table->boolean("auto_emergency_alert")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn([
                "status_epilepticus_duration_minutes",
                "emergency_seizure_count",
                "emergency_contact_info",
                "auto_emergency_alert",
            ]);
        });
    }
};

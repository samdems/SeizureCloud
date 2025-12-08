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
        Schema::table("medication_logs", function (Blueprint $table) {
            $table->dateTime("intended_time")->nullable()->after("taken_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("medication_logs", function (Blueprint $table) {
            $table->dropColumn("intended_time");
        });
    }
};

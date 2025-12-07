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
            $table
                ->time("morning_time")
                ->default("08:00")
                ->after("email_verified_at");
            $table
                ->time("afternoon_time")
                ->default("12:00")
                ->after("morning_time");
            $table
                ->time("evening_time")
                ->default("18:00")
                ->after("afternoon_time");
            $table->time("bedtime")->default("22:00")->after("evening_time");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn([
                "morning_time",
                "afternoon_time",
                "evening_time",
                "bedtime",
            ]);
        });
    }
};

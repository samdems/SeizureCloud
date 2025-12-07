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
        Schema::create("medication_schedules", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("medication_id")
                ->constrained()
                ->onDelete("cascade");
            $table->time("scheduled_time");
            $table->json("days_of_week")->nullable(); // [0,1,2,3,4,5,6] for Sun-Sat or null for daily
            $table->string("frequency")->default("daily"); // daily, weekly, as_needed
            $table->boolean("active")->default(true);
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("medication_schedules");
    }
};

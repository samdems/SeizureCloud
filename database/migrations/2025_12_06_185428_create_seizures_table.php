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
        Schema::create("seizures", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");

            // Timing information
            $table->dateTime("start_time");
            $table->dateTime("end_time")->nullable();
            $table->integer("duration_minutes")->nullable(); // Can be set separately

            // Seizure details
            $table
                ->tinyInteger("severity")
                ->comment("1-10 scale of how bad it was");
            $table->boolean("on_period")->default(false);

            // NHS contact information
            $table->boolean("nhs_contacted")->default(false);
            $table
                ->enum("nhs_contact_type", [
                    "GP",
                    "111",
                    "999",
                    "Epileptic Specialist",
                    "None",
                ])
                ->nullable();

            // Post-seizure information
            $table->dateTime("postictal_state_end")->nullable();
            $table->boolean("ambulance_called")->default(false);
            $table->boolean("slept_after")->default(false);

            // Additional notes
            $table->text("notes")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("seizures");
    }
};

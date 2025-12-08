<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("vital_type_thresholds", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("vital_type"); // e.g., "Resting BPM", "Blood Pressure"
            $table->float("low_threshold")->nullable();
            $table->float("high_threshold")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();

            // Ensure one threshold setting per user per vital type
            $table->unique(['user_id', 'vital_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("vital_type_thresholds");
    }
};

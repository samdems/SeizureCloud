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
        Schema::create("vitals", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table->string("type"); // e.g., "Resting BPM", "Blood Pressure"
            $table->float("value"); // e.g., 72.5
            $table->timestamp("recorded_at"); // When the vital was recorded
            $table->text("notes")->nullable(); // Optional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("vitals");
    }
};

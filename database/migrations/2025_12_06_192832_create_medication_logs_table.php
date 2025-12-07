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
        Schema::create("medication_logs", function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId("medication_id")
                ->constrained()
                ->onDelete("cascade");
            $table
                ->foreignId("medication_schedule_id")
                ->nullable()
                ->constrained()
                ->onDelete("set null");
            $table->dateTime("taken_at");
            $table->string("dosage_taken")->nullable();
            $table->boolean("skipped")->default(false);
            $table->string("skip_reason")->nullable();
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("medication_logs");
    }
};

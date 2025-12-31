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
        Schema::create("documents", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");

            // File information
            $table->string("title");
            $table->text("description")->nullable();
            $table->string("file_name");
            $table->string("file_path");
            $table->string("file_type");
            $table->integer("file_size"); // in bytes

            // Categorization
            $table
                ->enum("category", [
                    "medical_report",
                    "prescription",
                    "test_result",
                    "scan",
                    "letter",
                    "insurance",
                    "other",
                ])
                ->default("other");

            // Date information
            $table->date("document_date")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("documents");
    }
};

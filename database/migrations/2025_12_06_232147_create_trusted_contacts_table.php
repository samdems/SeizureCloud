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
        Schema::create("trusted_contacts", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->onDelete("cascade");
            $table
                ->foreignId("trusted_user_id")
                ->constrained("users")
                ->onDelete("cascade");
            $table->string("nickname")->nullable(); // Optional nickname for the trusted contact
            $table->text("access_note")->nullable(); // Optional note explaining the access
            $table->boolean("is_active")->default(true);
            $table->timestamp("granted_at");
            $table->timestamp("expires_at")->nullable(); // Optional expiration
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(["user_id", "trusted_user_id"]);

            // Add index for better performance
            $table->index(["trusted_user_id", "is_active"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("trusted_contacts");
    }
};

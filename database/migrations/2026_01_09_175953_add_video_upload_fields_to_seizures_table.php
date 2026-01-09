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
        Schema::table("seizures", function (Blueprint $table) {
            // Video upload fields
            $table->string("video_file_path")->nullable()->after("video_notes");
            $table
                ->string("video_public_token", 64)
                ->nullable()
                ->after("video_file_path");
            $table
                ->timestamp("video_expires_at")
                ->nullable()
                ->after("video_public_token");

            // Index for performance on token lookups
            $table->index("video_public_token");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("seizures", function (Blueprint $table) {
            $table->dropIndex(["video_public_token"]);
            $table->dropColumn([
                "video_file_path",
                "video_public_token",
                "video_expires_at",
            ]);
        });
    }
};

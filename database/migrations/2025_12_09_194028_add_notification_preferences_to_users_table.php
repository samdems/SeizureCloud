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
            $table->boolean("notify_medication_taken")->default(true);
            $table->boolean("notify_seizure_added")->default(true);
            $table
                ->boolean("notify_trusted_contacts_medication")
                ->default(true);
            $table->boolean("notify_trusted_contacts_seizures")->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn([
                "notify_medication_taken",
                "notify_seizure_added",
                "notify_trusted_contacts_medication",
                "notify_trusted_contacts_seizures",
            ]);
        });
    }
};

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
        Schema::table("vitals", function (Blueprint $table) {
            $table->float("low_threshold")->nullable()->after("value");
            $table->float("high_threshold")->nullable()->after("low_threshold");
            $table->string("status")->nullable()->after("high_threshold"); // normal, too_low, too_high
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("vitals", function (Blueprint $table) {
            $table->dropColumn(["low_threshold", "high_threshold", "status"]);
        });
    }
};

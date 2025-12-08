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
        Schema::table("vital_type_thresholds", function (Blueprint $table) {
            $table->float("systolic_low_threshold")->nullable()->after("high_threshold");
            $table->float("systolic_high_threshold")->nullable()->after("systolic_low_threshold");
            $table->float("diastolic_low_threshold")->nullable()->after("systolic_high_threshold");
            $table->float("diastolic_high_threshold")->nullable()->after("diastolic_low_threshold");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("vital_type_thresholds", function (Blueprint $table) {
            $table->dropColumn([
                "systolic_low_threshold",
                "systolic_high_threshold",
                "diastolic_low_threshold",
                "diastolic_high_threshold"
            ]);
        });
    }
};

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
            $table->float("systolic_value")->nullable()->after("value");
            $table->float("diastolic_value")->nullable()->after("systolic_value");
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
            $table->dropColumn(["systolic_value", "diastolic_value"]);
        });
    }
};

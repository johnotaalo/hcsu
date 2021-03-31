<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMotorbikePlate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("pm_data")->table('VSR_01', function (Blueprint $table) {
            $table->string("MOTOR_BIKE_PLATE")->default(0)->nullable()->after("COMMENTS");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection("pm_data")->table('VSR_01', function (Blueprint $table) {
            $table->dropColumn(["MOTOR_BIKE_PLATE"]);
        });
    }
}

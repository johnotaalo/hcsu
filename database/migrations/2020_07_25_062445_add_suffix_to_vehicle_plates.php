<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSuffixToVehiclePlates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->table('vehicle_plates', function (Blueprint $table) {
            $table->string("suffix")->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('vehicle_plates', function (Blueprint $table) {
            $table->dropColumn(['suffix']);
        });
    }
}

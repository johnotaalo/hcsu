<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrencyToVehicle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DF_02_Vehicles', function (Blueprint $table) {
            $table->string('CURRENCY')->after('VEHICLE_SEATING')->nullable();
            $table->string('VEHICLE_CARRYING')->after('CURRENCY')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DF_02_Vehicles', function (Blueprint $table) {
            $table->dropColumn(['CURRENCY', 'VEHICLE_CARRYING']);
        });
    }
}

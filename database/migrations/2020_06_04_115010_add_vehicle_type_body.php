<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehicleTypeBody extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->table('ref_body_type', function (Blueprint $table) {
            $table->integer('VEHICLE_TYPE');
            $table->foreign('VEHICLE_TYPE')->references('ID')->on('ref_veh_type');
        });

        //  Schema::connection('2019')->table('ref_veh_type', function (Blueprint $table) {
        //     $table->index('ID');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('ref_body_type', function (Blueprint $table) {
            $table->dropColumns(['VEHICLE_TYPE']);
        });
    }
}

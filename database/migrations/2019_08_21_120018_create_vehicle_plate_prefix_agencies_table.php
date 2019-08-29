<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclePlatePrefixAgenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->create('vehicle_plate_prefix_agencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('prefix_id');
            $table->string('host_country_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->dropIfExists('vehicle_plate_prefix_agencies');
    }
}

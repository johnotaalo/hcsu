<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePro1BVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DF_02_Vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->string('ENGINE_NO');
            $table->string('CHASSIS_NO');
            $table->integer('MAKE_MODEL_ID');
            $table->integer('COLOR_ID');
            $table->string('YOM');
            $table->string('FUEL');
            $table->string('RATING');
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
        Schema::connection('pm_data')->dropIfExists('DF_02_Vehicles');
    }
}

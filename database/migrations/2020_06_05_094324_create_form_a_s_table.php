<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('VSR_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('PRO_1B_CASE_NO')->nullable();
            $table->integer('HOST_COUNTRY_ID');
            $table->string('SERIAL_NO');
            $table->string('USE_ROAD');
            $table->string('USE_ESTATE');
            $table->string('USE_TOWN');
            $table->string('USE_DISTRICT');
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
        Schema::connection('pm_data')->dropIfExists('VSR_01');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirportPassLocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('AP_01_LOCALS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->string('NV_SERIAL_NO');
            $table->string('CLIENT_LAST_NAME');
            $table->string('CLIENT_OTHER_NAMES');
            $table->string('CLIENT_ORGANIZATION');
            $table->string('IDENTIFICATION');
            $table->string('PREVIOUS_PASS_NO')->nullable();
            $table->string('MOBILE_NO');
            $table->string('APPLICATION_TYPE');
            $table->string('APPLICATION_YEAR');
            $table->text('COMMENTS')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('AP_01_LOCALS');
    }
}

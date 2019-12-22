<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkPermitApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('IM_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->string('TYPE');
            $table->text('NV_SERIAL_NO');
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
        Schema::connection('pm_data')->dropIfExists('IM_01');
    }
}

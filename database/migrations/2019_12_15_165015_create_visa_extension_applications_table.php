<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisaExtensionApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('IM_05', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->text('KENYA_ADDRESS');
            $table->date('DATE_OF_ENTRY');
            $table->string('PORT_OF_ENTRY');
            $table->text('EXTENDING_REASON');
            $table->integer('PERIOD');
            $table->string('PERIOD_UNITS');
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
        Schema::connection('pm_data')->dropIfExists('IM_05');
    }
}

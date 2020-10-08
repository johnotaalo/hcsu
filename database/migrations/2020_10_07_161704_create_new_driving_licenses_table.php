<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewDrivingLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DL_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->string('LICENSE_NO')->nullable();
            $table->date('DATE_OF_ISSUE')->nullable();
            $table->date('EXPIRY_DATE')->nullable();
            $table->integer('DRIVING_YEARS')->nullable();
            $table->string('COUNTRY_OF_ISSUE')->nullable();
            $table->string('DL_CLASSES')->nullable();
            $table->string('COMPETENCY_NO')->nullable();
            $table->string('COURT_CONVICTION')->nullable();
            $table->string('COURT_DISQUALIFICATION')->nullable();
            $table->string('DISABILITY')->nullable();
            $table->string('EYESIGHT')->nullable();
            $table->string('HAND_FOOT')->nullable();
            $table->text('HAND_FOOT_DETAILS')->nullable();
            $table->string('MENTAL_HEALTH')->nullable();
            $table->text('MENTAL_HEALTH_DETAILS')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DL_01');
    }
}

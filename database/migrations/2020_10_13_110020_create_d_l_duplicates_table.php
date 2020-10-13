<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDLDuplicatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DL_03', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->string('REASON')->nullable();
            $table->string('DL_NO')->nullable();
            $table->date('ISSUE_DATE')->nullable();
            $table->text('DETAILS')->nullable();
            $table->string('SUBMIT_TO_NTSA')->nullable();
            $table->text('SUBMIT_TO_NTSA_COMMENTS')->nullable();
            $table->date('NEW_DL_ISSUE_DATE')->nullable();
            $table->date('NEW_DL_EXPIRY_DATE')->nullable();
            $table->string('COLLECTED_BY')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DL_03');
    }
}

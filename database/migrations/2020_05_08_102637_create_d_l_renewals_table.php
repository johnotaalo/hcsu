<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDLRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DL_02', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->boolean('SUBMIT_TO_NTSA')->nullable();
            $table->longText('SUBMIT_TO_NTSA_COMMENTS')->nullable();
            $table->boolean('NTSA_APPROVED')->nullable();
            $table->text('NTSA_APPROVED_COMMENTS')->nullable();
            $table->date('ISSUE_DATE')->nullable();
            $table->date('EXPIRY_DATE')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DL_02');
    }
}

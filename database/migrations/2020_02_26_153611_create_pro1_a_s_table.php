<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePro1ASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DF_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('CASE_NO');
            $table->integer('CLEARING_AGENT');
            $table->string('AIRWAY_BILL_NO');
            $table->string('INVOICE_NO');
            $table->string('PORT_OF_CLEARANCE');
            $table->string('NV_SERIAL_NO');
            $table->integer('SPIRITS')->nullable();
            $table->integer('WINES')->nullable();
            $table->integer('BEERS')->nullable();
            $table->integer('TOBACCO')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->string('IPMIS_NO')->nullable();
            $table->string('IPMIS_STATUS')->nullable();
            $table->longText('IPMIS_STATUS_COMMENT')->nullable();
            $table->string('IPMIS_STATUS_REASON')->nullable();
            $table->boolean('DOCUMENT_COLLECTED')->nullable();
            $table->string('DOCUMENT_COLLECTED_BY')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DF_01');
    }
}

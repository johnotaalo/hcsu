<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePro1BSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('DF_02', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->string('TYPE_OF_GOODS');
            $table->string('NV_SERIAL_NO');
            $table->boolean('CLIENT_ON_IPMIS');
            $table->integer('CLEARING_AGENT')->nullable();
            $table->string('AIRWAY_BILL_NO')->nullable();
            $table->string('INVOICE_NO')->nullable();
            $table->string('PORT_OF_CLEARANCE')->nullable();
            $table->string('SERIAL_NO')->nullable();
            $table->string('CARRIER')->nullable();
            $table->string('BONDED_WAREHOUSE_NO')->nullable();
            $table->text('MERCHANDISE_DESCRIPTION')->nullable();
            $table->text('EXTRA_COMMENTS')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENTS')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('DF_02');
    }
}

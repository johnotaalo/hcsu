<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNODSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('AC_03', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->date('DATE_OF_DEPARTURE');
            $table->longText('NEW_ADDRESS');
            $table->boolean('DIPLOMATIC_ID_STATUS');
            $table->longText('DEPENDENTS');
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->boolean('SUBMIT_TO_MOFA')->nullable();
            $table->longText('SUBMIT_TO_MOFA_COMMENT')->nullable();
            $table->string('IPMIS_NO')->nullable();
            $table->boolean('IPMIS_STATUS')->nullable();
            $table->longText('IPMIS_STATUS_COMMENT')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('AC_03');
    }
}

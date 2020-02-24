<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkPermitRenewalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('IM_01_RENEWALS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('CASE_NO');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('NV_SERIAL_NO');
            $table->string('TYPE')->nullable();
            $table->boolean('STAFF_ON_IPMIS')->nullable();
            $table->longText('ADDITIONAL_COMMENTS')->nullable();
            $table->text('DEPENDENTS')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->boolean('PASSPORTS_SUBMITTED')->nullable();
            $table->longText('PASSPORTS_SUBMITTED_COMMENT')->nullable();
            $table->boolean('MOFA_STATUS')->nullable();
            $table->longText('MOFA_STATUS_COMMENT')->nullable();
            $table->string('MOFA_STATUS_ACTION')->nullable();
            $table->boolean('PASSPORT_COLLECTED')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('IM_01_RENEWALS');
    }
}

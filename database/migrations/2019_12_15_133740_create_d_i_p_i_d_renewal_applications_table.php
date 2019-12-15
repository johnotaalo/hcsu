<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDIPIDRenewalApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('AC_02', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->text('SUBMITTED_DOCS')->nullable();
            $table->text('NV_SERIAL_NO')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->boolean('IPMIS_STATUS')->nullable();
            $table->text('IPMIS_STATUS_COMMENT')->nullable();
            $table->string('IPMIS_STATUS_REASON')->nullable();
            $table->string('IPMIS_CASE_NO')->nullable();
            $table->string('APPROVAL_DOCUMENT')->nullable();
            $table->string('CARD_NO')->nullable();
            $table->date('ISSUE_DATE')->nullable();
            $table->date('EXPIRY_DATE')->nullable();
            $table->boolean('MOFA_COLLECTED')->nullable();
            $table->string('MOFA_COLLECTED_BY')->nullable();
            $table->boolean('CARD_COLLECTED')->nullable();
            $table->string('CARD_COLLECTED_BY')->nullable();
            $table->date('MOFA_COLLECTION_DATE')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('AC_02');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpmisDetailsToIm01Renewal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01_RENEWALS', function (Blueprint $table) {
            $table->string('IPMIS_NUMBER')->after('PASSPORTS_SUBMITTED')->nullable();
            $table->string('IPMIS_STATUS')->after('IPMIS_NUMBER')->nullable();
            $table->string('IPMIS_STATUS_REASON')->after('IPMIS_STATUS')->nullable();
            $table->longText('IPMIS_STATUS_COMMENT')->after('IPMIS_STATUS_REASON')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_01_RENEWALS', function (Blueprint $table) {
            $table->dropColumn(['IPMIS_NUMBER', 'IPMIS_STATUS', 'IPMIS_STATUS_REASON', 'IPMIS_STATUS_COMMENT']);
        });
    }
}

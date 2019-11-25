<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpmisNumberPin01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->string('IPMIS_NO')->after('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->string('IPMIS_STATUS')->after('IPMIS_NO')->nullable();
            $table->string('IPMIS_COMMENT')->after('IPMIS_STATUS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->dropColumn(['IPMIS_NO', 'IPMIS_STATUS', 'IPMIS_COMMENT']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpmisNoComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->boolean('IPMIS_STATUS')->after('MANAGER_COMMENT')->nullable();
            $table->string('IPMIS_NO')->after('IPMIS_STATUS')->nullable();
            $table->text('IPMIS_COMMENT')->after('IPMIS_NO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->dropColumn(['IPMIS_STATUS', 'IPMIS_NO', 'IPMIS_COMMENT']);
        });
    }
}

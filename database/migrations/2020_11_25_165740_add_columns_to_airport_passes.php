<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAirportPasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AP_01', function (Blueprint $table) {
            $table->string('APPLICATION_TYPE')->after('NV_SERIAL_NO')->nullable();
            $table->string('PREVIOUS_PASS')->after('APPLICATION_TYPE')->nullable();
            $table->string('ADDITIONAL_COMMENTS')->after('PREVIOUS_PASS')->nullable();
            $table->string('YEAR')->after('PREVIOUS_PASS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AP_01', function (Blueprint $table) {
            $table->dropColumn(['APPLICATION_TYPE', 'PREVIOUS_PASS', 'ADDITIONAL_COMMENTS', 'YEAR']);
        });
    }
}

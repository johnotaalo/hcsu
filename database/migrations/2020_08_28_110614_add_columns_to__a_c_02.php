<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAC02 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_02', function (Blueprint $table) {
            $table->string('APPLICATION_TYPE')->after('NV_SERIAL_NO')->nullable();
            $table->string('REPLACEMENT_REASON')->after('APPLICATION_TYPE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AC_02', function (Blueprint $table) {
            $table->dropColumn(['REPLACEMENT_REASON', 'APPLICATION_TYPE']);
        });
    }
}

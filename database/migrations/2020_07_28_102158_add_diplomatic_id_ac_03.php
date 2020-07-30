<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDiplomaticIdAc03 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_03', function (Blueprint $table) {
            $table->longText('DIPLOMATIC_IDS')->after('DEPENDENTS');
            $table->dropColumn(['DIPLOMATIC_ID_STATUS']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AC_03', function (Blueprint $table) {
            $table->dropColumn(['DIPLOMATIC_IDS']);
            $table->boolean('DIPLOMATIC_ID_STATUS')->after('NEW_ADDRESS');
        });
    }
}

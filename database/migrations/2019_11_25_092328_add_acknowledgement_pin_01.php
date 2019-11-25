<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcknowledgementPin01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->date('ACKNOWLEDGEMENT_DATE')->after('IPMIS_COMMENT')->nullable();
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
            $table->dropColumn('ACKNOWLEDGEMENT_DATE');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEndorsementCaseNoIM01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->integer('ENDORSEMENT_CASE_NO')->after('ISSUE_DATE')->nullable();
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
            $table->dropColumn(['ENDORSEMENT_CASE_NO']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCardCollectionDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_01', function (Blueprint $table) {
            $table->date('MOFA_COLLECTION_DATE')->after('CARD_COLLECTED_BY')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('AC_01', function (Blueprint $table) {
            $table->dropColumn(['MOFA_COLLECTION_DATE']);
        });
    }
}

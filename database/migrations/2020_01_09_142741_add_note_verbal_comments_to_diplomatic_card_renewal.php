<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNoteVerbalCommentsToDiplomaticCardRenewal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('AC_01', function (Blueprint $table) {
            $table->longText('NV_COMMENTS')->after('NV_SERIAL_NO')->nullable();
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
            $table->dropColumn('NV_COMMENTS');
        });
    }
}

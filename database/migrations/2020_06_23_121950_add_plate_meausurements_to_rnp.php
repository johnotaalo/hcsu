<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlateMeausurementsToRnp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('RETURNED_PLATES_LIST', function (Blueprint $table) {
            $table->string('MEASUREMENTS')->after('PLATE_NO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('RETURNED_PLATES_LIST', function (Blueprint $table) {
            $table->dropColumn(['MEASUREMENTS']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignedFileLink extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('RETURNED_PLATES', function (Blueprint $table) {
            $table->text('SIGNED_DOCUMENT')->after('RNP_DATE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('RETURNED_PLATES', function (Blueprint $table) {
            $table->dropColumn(['SIGNED_DOCUMENT']);
        });
    }
}

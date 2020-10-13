<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDLToDependent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->string('DL_NO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->dropColumn(['DL_NO']);
        });
    }
}

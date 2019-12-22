<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGenderToDependents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->string('GENDER')->nullable();
            $table->string('PLACE_OF_BIRTH')->nullable();
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
            $table->dropColumn('GENDER', 'DATE_OF_BIRTH');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeDateOfBirthNullableDependent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL_DEPENDENT', function (Blueprint $table) {
            $table->date('DATE_OF_BIRTH')->nullable()->change();
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
            //
        });
    }
}

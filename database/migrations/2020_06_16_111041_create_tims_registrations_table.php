<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimsRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('TIMS_REGISTRATION', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('DIP_ID_NO');
            $table->string('MOBILE_NO');
            $table->string('KRA_PIN_NO');
            $table->string('USERNAME');
            $table->string('PASSWORD');
            $table->date('REGISTRATION_DATE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('TIMS_REGISTRATION');
    }
}

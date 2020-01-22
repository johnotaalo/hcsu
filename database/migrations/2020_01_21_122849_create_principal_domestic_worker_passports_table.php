<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrincipalDomesticWorkerPassportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PRINCIPAL_DOMESTIC_WORKER_PASSPORTS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('PASSPORT_NO')->unique();
            $table->string('PLACE_OF_ISSUE');
            $table->string('COUNTRY_OF_ISSUE');
            $table->integer('PASSPORT_TYPE');
            $table->date('ISSUE_DATE');
            $table->date('EXPIRY_DATE');
            $table->timestamps();

            $table->foreign('HOST_COUNTRY_ID')->references('HOST_COUNTRY_ID')->on('PRINCIPAL_DOMESTIC_WORKER')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PRINCIPAL_DOMESTIC_WORKER_PASSPORTS');
    }
}

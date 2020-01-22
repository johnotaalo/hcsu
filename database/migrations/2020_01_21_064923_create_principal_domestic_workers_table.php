<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrincipalDomesticWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('PRINCIPAL_DOMESTIC_WORKER', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID')->unique();
            $table->integer('PRINCIPAL_ID');
            $table->string('LAST_NAME');
            $table->string('OTHER_NAMES');
            $table->string('ADDRESS');
            $table->string('EMAIL');
            $table->string('PHONE_NUMBER');
            $table->string('PLACE_OF_BIRTH');
            $table->date('DATE_OF_BIRTH');
            $table->string('NATIONALITY');
            $table->string('R_NO')->nullable();
            $table->string('PLACE_OF_EMPLOYMENT');
            $table->string('JOB_TITLE')->default('House Help');
            $table->string('JOB_DESCRIPTION')->default('Domestic Worker');
            $table->date('CONTRACT_START_DATE');
            $table->timestamps();

            $table->index('LAST_NAME');
            $table->index('OTHER_NAMES');
            $table->index('EMAIL');
            $table->index('R_NO');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('PRINCIPAL_DOMESTIC_WORKER');
    }
}

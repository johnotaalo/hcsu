<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrincipalDependentPassportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('DEPENDENT_PASSPORTS', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->string('DEPENDENT_ID');
            $table->string('PASSPORT_NO');
            $table->integer('PASSPORT_TYPE');
            $table->date('ISSUE_DATE');
            $table->date('EXPIRY_DATE');
            $table->string('PLACE_OF_ISSUE');
            $table->string('COUNTRY_OF_ISSUE');
            $table->integer('OLD_REF_ID')->nullable();
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
        Schema::dropIfExists('principal_dependent_passports');
    }
}

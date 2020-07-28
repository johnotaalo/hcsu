<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDependentDiplomaticCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('DEPENDENT_DIPLOMATIC_CARDS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('DIP_ID_NO');
            $table->date('ISSUE_DATE');
            $table->date('EXPIRY_DATE');
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
        Schema::dropIfExists('DEPENDENT_DIPLOMATIC_CARDS');
    }
}

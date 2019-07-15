<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrincipalDiplomaticCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(strtoupper('principal_diplomatic_cards'), function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->integer("HOST_COUNTRY_ID");
            $table->string("DIP_ID_NO");
            $table->date("ISSUE_DATE");
            $table->date("EXPIRY_DATE");
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
        Schema::dropIfExists(strtoupper('principal_diplomatic_cards'));
    }
}

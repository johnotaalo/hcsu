<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnedPlateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('RETURNED_PLATES_LIST', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('RETURNED_PLATE_ID');
            $table->integer('HOST_COUNTRY_ID');
            $table->string('PLATE_NO');
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
        Schema::dropIfExists('RETURNED_PLATES_LIST');
    }
}

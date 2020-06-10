<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->create('ref_currencies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CODE');
            $table->string('NAME');
            $table->string('SYMBOL');
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
        Schema::connection('2019')->dropIfExists('ref_currencies');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVATUserApplicationDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('VAT_01_USER_APPLICATION_DOCUMENTS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('APPLICATION_ID');
            $table->text('PATH');
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
        Schema::dropIfExists('VAT_01_USER_APPLICATION_DOCUMENTS');
    }
}

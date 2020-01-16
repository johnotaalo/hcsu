<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIPMISFunctionalitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('IPMIS_Functionality', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('PROCESS_UID');
            $table->string('PROCESS_NAME');
            $table->boolean('IPMIS_FUNCTIONAL')->default(true);
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
        Schema::dropIfExists('IPMIS_Functionality');
    }
}

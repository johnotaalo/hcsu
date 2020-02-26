<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePortsOfClearancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->create('ref_ports_of_clearance', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->text('PORT_OF_CLEARANCE');
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
        Schema::connection('2019')->dropIfExists('ref_ports_of_clearance');
    }
}

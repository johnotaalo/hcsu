<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternshipPassApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('IM_06', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->integer('CASE_NO');
            $table->string('SUPERVISOR_NAME');
            $table->string('NV_SERIAL');
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
        Schema::connection('pm_data')->dropIfExists('IM_06');
    }
}

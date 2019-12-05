<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDIPIDApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('AC_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CASE_NO');
            $table->string('HOST_COUNTRY_ID');
            $table->text('SUBMITTED_DOCS')->nullable();
            $table->text('NV_SERIAL_NO')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENT')->nullable();
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
        Schema::connection('pm_data')->dropIfExists('AC_01');
    }
}

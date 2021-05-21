<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFireArmsApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('FP_01', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID');
            $table->integer('CASE_NO');
            $table->text('NV_SERIAL_NO');
            $table->string('REQUEST_TYPE');
            $table->longText('REQUEST_DETAILS')->nullable();
            $table->longText('STAFF_MEMBER_DETAILS')->nullable();
            $table->boolean('MANAGER_APPROVAL')->nullable();
            $table->text('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->boolean('SUBMIT_TO_MOFA')->nullable();
            $table->text('SUBMIT_TO_MOFA_COMMENT')->nullable();
            $table->boolean('MOFA_APPROVAL')->nullable();
            $table->text('MOFA_APPROVAL_COMMENT')->nullable();
            $table->boolean('IG_APPROVAL')->nullable();
            $table->text('IG_APPROVAL_COMMENT')->nullable();
            $table->string('COLLECTED_BY');
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
        Schema::connection('pm_data')->dropIfExists('FP_01');
    }
}

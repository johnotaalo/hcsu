<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVATUserApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('VAT_01_USER_APPLICATION', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('USER_ID');
            $table->text('ACKNOWLEDGEMENT_LINK');
            $table->integer('CASE_NO');
            $table->boolean('APPROVED')->default(FALSE);
            $table->text('APPROVAL_COMMENT')->nullable();
            $table->string('USER_CLAIMED')->nullable();
            $table->dateTime('CLAIMED_AT')->nullable();
            $table->string('STATUS')->default('Pending');
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
        Schema::connection('pm_data')->dropIfExists('VAT_01_USER_APPLICATION');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUSERAPPLICATIONSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('USER_APPLICATIONS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('PROCESS_UID');
            $table->integer('HOST_COUNTRY_ID');
            $table->longText('COMMENT')->nullable();
            $table->string('APP_UID')->nullable();
            $table->string('APP_NUMBER')->nullable();
            $table->string('ASSIGNED_TO')->nullable();
            $table->longText('SUPERVISOR_COMMENTS')->nullable();
            $table->string('STATUS')->default('SUBMITTED'); // SUBMITTED or ASSIGNED or QUERIED or CANCELED
            $table->string('SUBMITTED_BY')->nullable();
            $table->string('AUTHENTICATION_SOURCE')->nullable(); // LDAP or USER or PM
            $table->string('CURRENT_USER')->default('SUPERVISOR')->nullable(); // SELF or SUPERVISOR or NULL
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
        Schema::connection('pm_data')->dropIfExists('USER_APPLICATIONS');
    }
}

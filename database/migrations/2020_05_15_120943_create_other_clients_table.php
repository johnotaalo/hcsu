<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('HOST_COUNTRY_ID')->unique();
            $table->string('LAST_NAME');
            $table->string('OTHER_NAMES');
            $table->integer('NATIONALITY');
            $table->date('DATE_OF_BIRTH');
            $table->string('TYPE');
            $table->string('AFFILIATED_AGENCY');
            $table->string('DESCRIPTION');
            $table->string('PASSPORT_NO');
            $table->date('ISSUE_DATE');
            $table->date('EXPIRY_DATE');
            $table->string('COUNTRY_OF_ISSUE');
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
        Schema::dropIfExists('other_clients');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmissionControlFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->create('CONTROL_FORMS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("CASE_NO");
            $table->integer("HOST_COUNTRY_ID");
            $table->string("USER_UID");
            $table->longText("DOCUMENTS");
            $table->text("AGREEMENT_ID")->nullable();
            $table->string("STATUS")->nullable();
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
        Schema::connection('pm_data')->dropIfExists('CONTROL_FORMS');
    }
}

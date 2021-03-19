<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevalidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection("pm_data")->create('DF_04', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("CASE_NO");
            $table->integer("HOST_COUNTRY_ID");
            $table->string("APPLICATION_TYPE");
            $table->string("INITIAL_SYSTEM");
            $table->integer("INITIAL_CASE_NO");
            $table->longText("COMMENTS")->nullable();
            $table->boolean("MANAGER_APPROVAL")->nullable();
            $table->longText("MANAGER_APPROVAL_COMMENTS")->nullable();
            $table->boolean("SUBMIT_TO_MOFA")->nullable();
            $table->longText("SUBMIT_TO_MOFA_COMMENTS")->nullable();
            $table->boolean("MOFA_STATUS")->nullable();
            $table->longText("MOFA_STATUS_COMMENTS")->nullable();
            $table->string("COLLECTED_BY")->nullable();
            $table->date("COLLECTION_DATE")->nullable();
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
        Schema::connection("pm_data")->dropIfExists('DF_04');
    }
}

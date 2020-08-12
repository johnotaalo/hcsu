<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdobeSignDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ADOBE_SIGN_DOCUMENTS', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('DOCUMENT_ID');
            $table->string('AGREEMENT_ID');
            $table->string('AGREEMENT_STATUS')->nullable();
            $table->longText('PAYLOAD')->nullable();
            $table->longText('URLS')->nullable();
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
        Schema::dropIfExists('ADOBE_SIGN_DOCUMENTS');
    }
}

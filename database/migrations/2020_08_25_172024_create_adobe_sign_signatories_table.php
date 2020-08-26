<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdobeSignSignatoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ADOBE_SIGN_SIGNATORIES', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("last_name");
            $table->string("other_names");
            $table->string("email")->unique();
            $table->boolean("status")->default(false);
            $table->string('location')->default('HCSU');
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
        Schema::dropIfExists('ADOBE_SIGN_SIGNATORIES');
    }
}

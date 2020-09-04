<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoutingColumnAdobeSignDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ADOBE_SIGN_DOCUMENTS', function (Blueprint $table) {
            $table->boolean('ROUTING')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ADOBE_SIGN_DOCUMENTS', function (Blueprint $table) {
            $table->dropColumn(['ROUTING']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusOnPrincipal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL', function (Blueprint $table) {
            $table->boolean('STATUS')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('PRINCIPAL', function (Blueprint $table) {
            $table->dropColumn(['STATUS']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPrincipal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('PRINCIPAL', function (Blueprint $table) {
            $table->text('PLACE_OF_BIRTH')->after('DATE_OF_BIRTH')->nullable();
            $table->integer('NATIONALITY')->after('PLACE_OF_BIRTH')->nullable();
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
            $table->dropColumn(['PLACE_OF_BIRTH', 'NATIONALITY']);
        });
    }
}

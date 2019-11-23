<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbbrevToCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->table('country', function (Blueprint $table) {
            $table->string('pm_abbrev')->after('official_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('country', function (Blueprint $table) {
            $table->dropColumn('pm_abbrev');
        });
    }
}

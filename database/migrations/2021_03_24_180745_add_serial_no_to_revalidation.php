<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSerialNoToRevalidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DF_04', function (Blueprint $table) {
            $table->text("NV_SERIAL_NO")->after("HOST_COUNTRY_ID");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DF_04', function (Blueprint $table) {
            $table->dropColumns(["NV_SERIAL_NO"]);
        });
    }
}

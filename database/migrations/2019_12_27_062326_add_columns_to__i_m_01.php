<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToIM01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->boolean('INCLUDE_PRINCIPAL')->after('NV_SERIAL_NO')->nullable();
            $table->text('DEPENDENTS')->after('INCLUDE_PRINCIPAL')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_01', function (Blueprint $table) {
            $table->dropColumn(['INCLUDE_PRINCIPAL', 'DEPENDENTS']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheckFlagAgencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->table('agencies', function (Blueprint $table) {
            $table->boolean('IS_ACTIVE')->default(true)->after('PHY_ADDRESS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('agencies', function (Blueprint $table) {
            $table->dropColumn(['IS_ACTIVE']);
        });
    }
}

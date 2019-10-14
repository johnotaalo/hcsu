<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Vat02ChangeAmountToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VAT_02', function (Blueprint $table) {
            $table->decimal("VAT_AMOUNT", 12, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VAT_02', function (Blueprint $table) {
            $table->float('VAT_AMOUNT', 12, 2)->change();
        });
    }
}

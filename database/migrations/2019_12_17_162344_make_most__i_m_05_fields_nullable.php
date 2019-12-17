<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeMostIM05FieldsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_05', function (Blueprint $table) {
            $table->text('KENYA_ADDRESS')->nullable()->change();
            $table->date('DATE_OF_ENTRY')->nullable()->change();
            $table->string('PORT_OF_ENTRY')->nullable()->change();
            $table->text('EXTENDING_REASON')->nullable()->change();
            $table->integer('PERIOD')->nullable()->change();
            $table->string('PERIOD_UNITS')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_05', function (Blueprint $table) {
            
        });
    }
}

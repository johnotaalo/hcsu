<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImmigrationSettlementColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_05', function (Blueprint $table) {
            $table->string('APPLICATION_TYPE')->after('HOST_COUNTRY_ID')->nullable();
            $table->string('IS_APPLICATION_FOR')->nullable();
            $table->string('IS_OCCUPATION')->nullable();
            $table->text('IS_ADDRESS_IN_COUNTRY_OF_RESIDENCE')->nullable();
            $table->text('IS_REASONS_FOR_VISITING')->nullable();
            $table->date('IS_DATE_OF_ARRIVAL')->nullable();
            $table->integer('IS_DURATION')->nullable();
            $table->string('IS_DURATION_UNITS')->nullable();
            $table->text('IS_PERMIT_ISSUED')->nullable();
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
            $table->dropColumn([
                'APPLICATION_TYPE',
                'IS_APPLICATION_FOR',
                'IS_OCCUPATION',
                'IS_ADDRESS_IN_COUNTRY_OF_RESIDENCE',
                'IS_REASONS_FOR_VISITING',
                'IS_DATE_OF_ARRIVAL',
                'IS_DURATION',
                'IS_DURATION_UNITS',
                'IS_PERMIT_ISSUED'
            ]);
        });
    }
}

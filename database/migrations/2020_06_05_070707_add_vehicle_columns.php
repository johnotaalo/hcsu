<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVehicleColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DF_02_Vehicles', function(Blueprint $table){
            $table->string('VEHICLE_CATEGORY')->after('RATING')->nullable();
            $table->string('VEHICLE_TYPE')->after('VEHICLE_CATEGORY')->nullable();
            $table->string('VEHICLE_WEIGHT')->after('VEHICLE_TYPE')->nullable();
            $table->string('VEHICLE_VALUE')->after('VEHICLE_WEIGHT')->nullable();
            $table->integer('COUNTRY_OF_ORIGIN')->after('VEHICLE_VALUE')->nullable();
            $table->string('ORIGINAL_REGISTRATION')->after('COUNTRY_OF_ORIGIN')->nullable();
            $table->string('VEHICLE_SEATING')->after('ORIGINAL_REGISTRATION')->nullable();
            $table->integer('BODY_TYPE')->after('VEHICLE_TYPE')->nullable();
            $table->integer('FORM_A_CASE_NO')->after('VEHICLE_SEATING')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DF_02_Vehicles', function(Blueprint $table){
            $table->dropColumn([
                'VEHICLE_CATEGORY',
                'VEHICLE_TYPE',
                'VEHICLE_WEIGHT',
                'VEHICLE_VALUE',
                'COUNTRY_OF_ORIGIN',
                'ORIGINAL_REGISTRATION',
                'VEHICLE_SEATING',
                'BODY_TYPE',
                'FORM_A_CASE_NO'
            ]);
        });
    }
}

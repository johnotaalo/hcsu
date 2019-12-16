<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToIM05 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_05', function (Blueprint $table) {
            $table->boolean('SUBMIT_TO_IMMIGRATION')->nullable();
            $table->text('SUBMIT_TO_IMMIGRATION_COMMENT')->nullable();
            $table->boolean('IMMIGRATION_STATUS')->nullable();
            $table->date('VISA_ISSUE_DATE')->nullable();
            $table->date('VISA_EXPIRY_DATE')->nullable();
            $table->text('IMMIGRATION_STATUS_COMMENT')->nullable();
            $table->boolean('PASSPORT_COLLECTED')->nullable();
            $table->string('PASSPORT_COLLECTED_BY')->nullable();
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
            $table->dropColumn(['SUBMIT_TO_IMMIGRATION', 'SUBMIT_TO_IMMIGRATION_COMMENT', 'IMMIGRATION_STATUS', 'VISA_ISSUE_DATE', 'VISA_EXPIRY_DATE', 'IMMIGRATION_STATUS_COMMENT', 'PASSPORT_COLLECTED', 'PASSPORT_COLLECTED_BY']);
        });
    }
}
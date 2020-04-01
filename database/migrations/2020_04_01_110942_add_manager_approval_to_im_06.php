<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagerApprovalToIm06 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('IM_06', function (Blueprint $table) {
            $table->integer('MANAGER_APPROVAL')->after('NV_SERIAL')->nullable();
            $table->longText('MANAGER_APPROVAL_COMMENT')->after('MANAGER_APPROVAL')->nullable();
            $table->integer('SUBMIT_TO_IMMIGRATION')->after('MANAGER_APPROVAL_COMMENT')->nullable();
            $table->longText('SUBMIT_TO_IMMIGRATION_COMMENT')->after('SUBMIT_TO_IMMIGRATION')->nullable();
            $table->integer('IMMIGRATION_FEEDBACK')->after('SUBMIT_TO_IMMIGRATION_COMMENT')->nullable();
            $table->integer('IMMIGRATION_FEEDBACK_COMMENT')->after('IMMIGRATION_FEEDBACK')->nullable();
            $table->boolean('PASSPORT_SUBMITTED')->after('IMMIGRATION_FEEDBACK_COMMENT')->nullable();
            $table->boolean('PASSPORT_RECEIVED')->after('PASSPORT_SUBMITTED')->nullable();
            $table->boolean('PASSPORT_COLLECTED')->after('PASSPORT_RECEIVED')->nullable();
            $table->string('PASSPORT_COLLECTED_BY')->after('PASSPORT_COLLECTED')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('IM_06', function (Blueprint $table) {
            $table->dropColumns(['MANAGER_APPROVAL', 'MANAGER_APPROVAL_COMMENT', 'SUBMIT_TO_IMMIGRATION', 'SUBMIT_TO_IMMIGRATION_COMMENT', 'IMMIGRATION_FEEDBACK', 'IMMIGRATION_FEEDBACK_COMMENT', 'PASSPORT_SUBMITTED', 'PASSPORT_RECEIVED', 'PASSPORT_COLLECTED', 'PASSPORT_COLLECTED_BY']);
        });
    }
}

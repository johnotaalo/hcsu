<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPIN01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->boolean('KRA_SUBMISSION')->nullable()->after('ACKNOWLEDGEMENT_DATE');
            $table->text('KRA_SUBMISSION_COMMENT')->nullable()->after('KRA_SUBMISSION');
            $table->date('KRA_SUBMISSION_DATE')->nullable()->after('KRA_SUBMISSION_COMMENT');
            $table->boolean('KRA_STATUS')->nullable()->after('KRA_SUBMISSION_DATE');
            $table->text('KRA_STATUS_COMMENT')->nullable()->after('KRA_STATUS');
            $table->boolean('PASSPORT_COLLECTED')->default(false)->after('KRA_STATUS_COMMENT');
            $table->string('PASSPORT_COLLECTED_BY')->nullable()->after('PASSPORT_COLLECTED');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('PIN_01', function (Blueprint $table) {
            $table->dropColumn(['KRA_SUBMISSION', 'KRA_SUBMISSION_COMMENT', 'KRA_SUBMISSION_DATE', 'KRA_STATUS', 'KRA_STATUS_COMMENT', 'PASSPORT_COLLECTED', 'PASSPORT_COLLECTED_BY']);
        });
    }
}

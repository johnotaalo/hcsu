<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceToLogbook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VSR_03', function (Blueprint $table) {
            $table->string('INVOICE_NO')->nullable();
            $table->string('LOGBOOK_NO')->nullable();
            $table->date('REGISTRATION_DATE')->nullable();
            $table->date('ISSUE_DATE')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VSR_03', function (Blueprint $table) {
            $table->dropColumn(['INVOICE_NO', 'LOGBOOK_NO', 'REGISTRATION_DATE', 'ISSUE_DATE']);
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentIdToVatuserdocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('VAT_01_USER_APPLICATION_DOCUMENTS', function (Blueprint $table) {
            $table->text('DOCUMENT_UID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('VAT_01_USER_APPLICATION_DOCUMENTS', function (Blueprint $table) {
            $table->dropColumn('DOCUMENT_UID');
        });
    }
}

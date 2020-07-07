<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToPRO1C extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pm_data')->table('DF_03', function (Blueprint $table) {
            $table->text('ADDITIONAL_COMMENTS')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pm_data')->table('DF_03', function (Blueprint $table) {
            $table->dropColumns(['ADDITIONAL_COMMENTS']);
        });
    }
}

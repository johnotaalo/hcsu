<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsValidityPassport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('principal_passport', function (Blueprint $table) {
            $table->date('ISSUE_DATE')->nullable();
            $table->date('EXPIRY_DATE')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('principal_passport', function (Blueprint $table) {
            $table->dropColumns(['ISSUE_DATE', 'EXPIRY_DATE', 'created_at', 'updated_at']);
        });
    }
}

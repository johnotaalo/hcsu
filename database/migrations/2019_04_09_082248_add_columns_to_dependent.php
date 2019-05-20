<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDependent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('principal_dependent', function (Blueprint $table) {
            $table->string('DIPLOMATIC_ID')->nullable();
            $table->string('PIN')->nullable();
            $table->string('RNO')->nullable();
        });

        Schema::table('principal', function (Blueprint $table) {
            $table->string('PIN')->nullable();
        });

        Schema::table('principal_contract', function (Blueprint $table) {
            $table->integer('GRADE_ID')->nullable();
            $table->string('GRADE')->nullable();
            $table->date('CONTRACT_FROM')->nullable();
            $table->date('CONTRACT_TO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('principal_dependent', function (Blueprint $table) {
            $table->dropColumn(['DIPLOMATIC_ID', 'PIN', 'RNO']);
        });

        Schema::table('principal', function (Blueprint $table) {
            $table->dropColumn(['PIN']);
        });

        Schema::table('principal_contract', function (Blueprint $table) {
            $table->dropColumn(['GRADE_ID', 'GRADE', 'CONTRACT_FROM', 'CONTRACT_TO']);
        });
    }
}

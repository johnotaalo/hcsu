<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsPrincipalContractAndRenewal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('principal_contract', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('principal_contract_renewals', function (Blueprint $table) {
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
        Schema::table('principal_contract', function (Blueprint $table) {
            $table->dropColumns(['created_at', 'updated_at']);
        });

        Schema::table('principal_contract_renewals', function (Blueprint $table) {
            $table->dropColumns(['created_at', 'updated_at']);
        });
    }
}

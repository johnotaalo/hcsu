<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIdToClearingAgents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('2019')->rename('unon_clearing_agents_lookup', 'ref_clearing_agents');
        Schema::connection('2019')->table('ref_clearing_agents', function (Blueprint $table) {
            $table->increments('ID')->first();
            $table->renameColumn('clearing_agent_name', 'CLEARING_AGENT_NAME');
            $table->renameColumn('clearing_agent_address', 'CLEARING_AGENT_ADDRESS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('2019')->table('ref_clearing_agents', function (Blueprint $table) {
            $table->dropColumn('ID');
            $table->renameColumn('CLEARING_AGENT_NAME', 'clearing_agent_name');
            $table->renameColumn('CLEARING_AGENT_ADDRESS', 'clearing_agent_address');
        });

        Schema::connection('2019')->rename('ref_clearing_agents', 'unon_clearing_agents_lookup');
    }
}

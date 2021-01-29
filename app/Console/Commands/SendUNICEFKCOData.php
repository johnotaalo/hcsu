<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ExportOrganizationData;
use Illuminate\Support\Facades\Mail;

use \App\Jobs\SendOrganizationData;

class SendUNICEFKCOData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senddata:unicef-kco';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron job sends data to UNICEF(KCO) daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = ((array)json_decode(\Storage::get('backup-settings.json')))['UNICEF-KCO'];

        $organizations = explode(',', $config->organizations);
        $recepients = explode(',', $config->recepients);
        $organizations = collect($organizations)->map(function($organization){
            return trim($organization);
        })->toArray();

        $recepients = collect($recepients)->map(function($recepient){
            return trim($recepient);
        })->toArray();

        SendOrganizationData::dispatch($organizations, $recepients, "UNICEF (KCO)", "UNICEF (KCO)");

        \Log::info("Cron is working fine!");

        $this->info('Cron Command Run successfully!');
    }
}

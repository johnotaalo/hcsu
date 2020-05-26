<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Up Organizations Data Dump Parameters';

    protected $fileName = "backup-settings.json";

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
        $config = $this->getSettings();
        $group = $this->ask('Please enter the group name');
        $enteredGroups = array_keys($config);
        $exists = in_array($group, $enteredGroups);
        if (!$exists) {
            $organizations = $this->ask('Enter the organization acronyms (COMMA SEPARATED)');
            $recepients = $this->ask('Enter the recepients emails (COMMA SEPARATED)');
            $config[$group] = [
                'recepients' => $recepients,
                'organizations' => $organizations
            ];

            \Storage::put($this->fileName, json_encode($config));
        }
    }

    private function getSettings(){
        $config = [];
        try {
            $config = (array)json_decode(\Storage::get($this->fileName));
        } catch (\Exception $ex) {
            // \Storage::put($this->fileName, json_encode([]));
        }

        return $config;
    }
}

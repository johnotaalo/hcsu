<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupControlForm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:control-form';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up control form';

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
        $config = [];
        try{
            $config = (array)json_decode(\Storage::get("control-forms.json"));
        }catch(\Exception $ex){
        }

        $libraryDocumentId = $this->ask("Please input the Library Document ID");
        $config['documentId'] = $libraryDocumentId;

        \Storage::put("control-forms.json", json_encode($config));

        $this->info("Successfully input document");
    }
}

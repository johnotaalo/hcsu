<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Setup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'self:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $processes = ['VAT', 'Blanket VAT'];

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
        $processConfig = [];
        
        try{
            $processConfig = (array)json_decode(\Storage::get('processes.json'));          
        }catch(\Exception $ex){
            \Storage::put('processes.json', json_encode($this->createProcessConfigArray([]))); 
        }

        if ($processConfig) {
            $this->info('You have some processes already set up. If you do not want to change them, leave the value as blank');
            foreach ($processConfig as $key => $value) {
                $this->info("[{$key}] => {$value}");
            }
        }

        $processConfig = $this->createProcessConfigArray($processConfig);

        foreach ($this->processes as $process) {
            $value = $this->ask("Enter Process UID for {$process}");
            $taskValue = $this->ask("Enter Task UID for {$process} Main Task");
            $outputDocumentValue = $this->ask("Enter Output Document UID for {$process} Main Task");
            $inputDocumentValue = $this->ask("Enter Input Document UID for {$process} Main Task");
            if($value != ""){
                $processConfig[$process]['id'] = $value;
                $processConfig[$process]['task'] = $taskValue;
                $processConfig[$process]['documents']['output'] = $outputDocumentValue;
                $processConfig[$process]['documents']['input'] = $inputDocumentValue;
            }
        }

        if (\Storage::put('processes.json', json_encode($processConfig))) {
            $this->info("Successfully setup processes");
        }else{
            $this->error("There was an error setting up processes");
        }
    }

    private function createProcessConfigArray($array){
        
        foreach ($this->processes as $key => $value) {
            if (!array_key_exists($value, $array)) {
                $array[$value]['id'] = "";
                $array[$value]['task'] = "";
            }
        }

        return $array;
    }
}

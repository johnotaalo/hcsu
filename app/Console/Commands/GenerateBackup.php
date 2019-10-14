<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
ini_set('memory_limit', '30000M');

class GenerateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'selfbackup:data {--ignore-views}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the old processmaker db and dump it into the development server';

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
        $ignoreViews = $this->option('ignore-views');
        $ignored_string = "";
        if($ignoreViews){
            $views= \DB::connection('old_pm')->select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '".env('OLD_PM_DB_DATABASE')."' AND TABLE_TYPE = 'VIEW'");
            foreach($views as $view){
                $ignored_string = $ignored_string . "--ignore-table=" . env('OLD_PM_DB_DATABASE') . ".{$view->TABLE_NAME} ";
            }
        }

        $command = "mysqldump --host ".env('OLD_PM_DB_HOST')." -u".env('OLD_PM_DB_USERNAME')." -p'".env('OLD_PM_DB_PASSWORD')."' ".env('OLD_PM_DB_DATABASE')." {$ignored_string} > ".storage_path('app/temp/')."old_pm.sql";


        $process = new Process($command);
        $process->setTimeout(3600);
        $process->run();
        $this->info('Dumping data started');
        if (!$process->isSuccessful()) {
            $this->info('Oops! There was an error preparing your dump!');
            throw new ProcessFailedException($process);
        }

        // Dump was successful. Upload data into the dev server.
        $this->info('Data dumped successfully');
        // Get the sql dump and run it as a query
        $this->info('Dumping data into database...');
        \DB::connection('dev_pm')->unprepared(\Storage::get('temp/old_pm.sql'));
        // echo $process->getOutput();
    }
}

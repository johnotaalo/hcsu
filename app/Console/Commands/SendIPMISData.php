<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendIPMISData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senddata:ipmis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $recepients = ['jean.mbavu@un.org', 'chrispine.otaalo@un.org'];
        $data = \DB::connection('pm_data')->table('VW_IPMIS_CASE_STATUS')->get();

        $exportData = new \App\Exports\ExportIPMISData($data);
        $date = date('Y-m-d');
        $datetime = date('Y_m_d_H_i_s');
        $filename = "data-exports/{$date}/HCSU/IPMIS_{$datetime}.xlsx";
        \Excel::store($exportData, $filename);

        Mail::to($recepients)->send(new \App\Mail\SendHCSUData($filename));
    }
}

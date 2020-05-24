<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\ExportOrganizationData;
use Illuminate\Support\Facades\Mail;

class SendUNSOSDataCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'senddata:unsos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This cron job sends data to UNSOS every Tuesday and Thursday';

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
        // $sampleFile = "data-exports/2020-01-27/UNSOS/New_PM_2020_01_27_16_41_38.xlsx";
        // Mail::to('john.otaalo@strathmore.edu')->send(new \App\Mail\DataDumpSent($sampleFile));
        // die;
        $statuses = ['TO_DO', 'COMPLETED'];
        $years = [];

        for ($i=2019; $i < date('Y'); $i++) {
            $years[] = $i;
        }

        $organizations = ['UNSOS', 'UNSOA', 'UNSOM'];

        $queryBuilder = \DB::connection('pm_data')->table('VW_CASE_INFO');
        $data = $queryBuilder->whereIn('agency', $organizations)
                                        ->whereIn('CASE_STATUS', $statuses)
                                        ->whereIn(\DB::raw('YEAR(CASE_START_DATE)'), $years)
                                        ->get();
        $exportData = new \App\Exports\OrganizationDataExport($data);
        $date = date('Y-m-d');
        $datetime = date('Y_m_d_H_i_s');
        $filename = "data-exports/{$date}/UNSOS/New_PM_{$datetime}.xlsx";
        \Excel::store($exportData, $filename);

        Mail::to('john.otaalo@strathmore.edu')->send(new \App\Mail\DataDumpSent($filename));

        \Log::info("Cron is working fine!");

        $this->info('Demo:Cron Command Run successfully!');
    }
}

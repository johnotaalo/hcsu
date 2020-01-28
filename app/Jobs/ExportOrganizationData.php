<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportOrganizationData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $organizations;
    protected $group;
    // public $connection = 'database';

    public function __construct($organizations, $group)
    {
        $this->organizations = $organizations;
        $this->group = $group;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $statuses = ['TO_DO', 'COMPLETED'];
        $years = [];

        for ($i=2019; $i < date('Y'); $i++) {
            $years[] = $i;
        }

        $queryBuilder = \DB::connection('pm_data')->table('VW_CASE_INFO');
        $data = $queryBuilder->whereIn('agency', $this->organizations)
                                        ->whereIn('CASE_STATUS', $statuses)
                                        ->whereIn(\DB::raw('YEAR(CASE_START_DATE)'), $years)
                                        ->get();
        $exportData = new \App\Exports\OrganizationDataExport($data);
        $date = date('Y-m-d');
        $datetime = date('Y_m_d_H_i_s');
        \Excel::store($exportData, "data-exports/{$date}/{$this->group}/New_PM_{$datetime}.xlsx");
    }
}

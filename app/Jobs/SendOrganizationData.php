<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\ExportOrganizationData;
use Illuminate\Support\Facades\Mail;

class SendOrganizationData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $organizations, $recepients, $searchBy, $group;
    public function __construct($organizations, $recepients, $searchBy, $group)
    {
        $this->organizations = $organizations;
        $this->recepients = $recepients;
        $this->searchBy = $searchBy;
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

        $date = date('Y-m-d');
        $datetime = date('Y_m_d_H_i_s');

        $filename = "data-exports/{$date}/{$this->group}/New_PM_{$date}.xlsx";
        $oldDataFileName = "data-exports/{$date}/{$this->group}/Old_PM_{$date}.xlsx";

        \Log::info("Looking for files: New PM {$filename}; Old PM {$oldDataFileName}");
        if (!\Storage::exists($filename) && !\Storage::exists($oldDataFileName)) {
            \Log::info("No files found. Querying database for data");
            \Log::info("Querying new database...");
            $queryBuilder = \DB::connection('pm_data')->table('VW_CASE_INFO')->select('CASE_UID', 'CASE_NO', 'CASE_STATUS', 'OWNER_LAST_NAME', 'OWNER_OTHER_NAMES', 'INDEX_NO', 'agency', 'application_by', 'grade', 'designation', 'contract_type', 'residence_no', 'CASE_START_DATE', 'CASE_END_DATE', 'PRO_UID', 'PRO_TITLE');
            $data = $queryBuilder->where('agency', 'LIKE', "{$this->searchBy}%")
                                            ->whereIn('CASE_STATUS', $statuses)
                                            ->get();
            \Log::info("Successfully queried data from new database");
            \Log::info("Querying old database");
            $oldDataQuery = \DB::connection('old_pm')->table('vw_case_data_no_task');
            $oldData = $oldDataQuery->where('agency', 'LIKE', "{$this->searchBy}%")
                                        ->whereIn('CASE_STATUS', $statuses)
                                        ->get();
            \Log::info("Successfully queried data from old database");

            $exportData = new \App\Exports\OrganizationDataExport($data);
            \Excel::store($exportData, $filename);

            \Log::info("Successfully generated new database excel");

            $oldDataExport = new \App\Exports\OrganizationDataExport($oldData);
            \Excel::store($oldDataExport, $oldDataFileName);
            \Log::info("Successfully generated old database excel");
        }else{
            \Log::info("Excel data exists");
        }

        \Log::info("Sending emails to recepients");
        $response = Mail::to($this->recepients)->send(new \App\Mail\DataDumpSent($filename, $oldDataFileName));
        \Log::debug($response);
        // try{
            
        //     \Log::info("Successfully sent email to recepients: ", $recepients);
        // }catch(\Exception $ex){
        //     \Log::error("Could not send email... Retrying", $ex);
        //     // throw new \Exception;
        // }
        
    }
}

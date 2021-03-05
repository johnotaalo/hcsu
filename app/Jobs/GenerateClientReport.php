<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

use App\User;

class GenerateClientReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $user;
    private $parameters;
    public function __construct($parameters, User $user)
    {
        $this->parameters = $parameters;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $years = $processes = $agencies = null;
        if (isset($this->parameters['years'])){
            $years = collect($this->parameters['years'])->map(function ($year){
                return $year['text'];
            })->toArray();
        }

        if (isset($this->parameters['processes'])){
            $processes = collect($this->parameters['processes'])->map(function($process){
                return $process['PRO_UID'];
            })->toArray();
        }

        if(isset($this->parameters['agencies'])){
            $agencies = collect($this->parameters['agencies'])->map(function ($agency){
                return $agency["ACRONYM"];
            })->toArray();
        }else{
            if ($this->user->type == "Focal point"){
                $agencies = $this->user->focal_point->agencies->map(function ($agency){
                    return $agency->ACRONYM;
                })->toArray();
            }
        }

        $action = $this->parameters['action'];

        $queryBuilder = \DB::connection('pm_data')->table("VW_APPLICATION_REPORTING")->select("HOST_COUNTRY_ID", "CASE_NO", "CLIENT", "APPLICATION_BY", "GRADE", "AGENCY", "CASE_START_DATE", "CASE_END_DATE", "CASE_STATUS", "PRO_TITLE", "PROCESS_CATEGORY", "TASK", "LOCATION");
        if ($years){
            $queryBuilder->whereIn(\DB::raw("YEAR(CASE_START_DATE)"), $years);
        }

        if ($processes){
            $queryBuilder->whereIn("PRO_UID", $processes);
        }

        if ($agencies){
            $queryBuilder->whereIn(\DB::raw("BINARY(AGENCY)"), $agencies);
        }

        $applications = $queryBuilder->get();
        $exportData = new \App\Exports\ClientDataExport($applications);
        if ($action == 'download'){
            return $exportData;
        }else{
//            Send email to user
            $email = $this->user->email;
            $excelContent = \Excel::raw($email, \Maatwebsite\Excel\Excel::XLSX);
            \Log::debug("Sending data to user email: {$email}");
            $response = Mail::to([$email])->send(new \App\Mail\SendExcelDump($this->user, $excelContent));
            \Log::debug($response);
        }

    }
}

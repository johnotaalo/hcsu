<?php

namespace App\Console\Commands;

use App\FormTemplate;
use App\Helpers\HCSU\AdobeSign\AdobeClient;
use App\Helpers\HCSU\ProcessMaker;
use Illuminate\Console\Command;

class CheckSubmissionControlForms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adobesign:checksubmission-control-forms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'his checks the status of all pending adobe sign documents and downloads any documents that may have been downloaded';

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
        try {
            $controlForms = \App\Models\SubmissionControlForm::where('STATUS', 'OUT_FOR_SIGNATURE')->get();

            if($controlForms){
                $message = "There are " . count($controlForms) . " control form(s) yet to be signed and downloaded";
                $this->info($message);
                \Log::debug($message);

                foreach ($controlForms as $form){
                    $agreementDetails = AdobeClient::getAgreementDetails($form->AGREEMENT_ID);
                    $case = \App\Models\PM\Application::where('APP_NUMBER', $form->CASE_NO)->first();
                    if ($agreementDetails->status == "SIGNED"){
                        \Log::debug("Case: {$case->APP_UID}");
                        $template = FormTemplate::where('process', $case->PRO_UID)->first();

                        $this->info("Downloading signed document. Agreement ID: {$agreementDetails->agreementId}");
                        \Log::debug("Downloading signed document. Agreement ID: {$agreementDetails->agreementId}");

                        $documentContent = AdobeClient::downloadSignedDocument($agreementDetails->agreementId);

                        $path = "adobe-sign/documents/{$form->CASE_NO}-Control-Form-signed.pdf";

                        \Storage::put($path, $documentContent);

                        $this->info("Signed Document saved in: " . storage_path("app/" . $path));
                        \Log::debug("Signed Document saved in: " . storage_path("app/" . $path));

                        $response = ProcessMaker::uploadGeneratedForm($case->APP_UID, $template->task, $template->input_document, $path);
                        \Log::debug("ProcessMaker upload documents: " . json_encode($response));
                    }else{
                        $this->info("Agreement ID: {$agreementDetails->agreementId} has not been signed yet");
                        \Log::debug("Agreement ID: {$agreementDetails->agreementId} has not been signed yet.");
                    }

                    $form->STATUS = $agreementDetails->status;
                    $form->save();
                }
            }
            else{
                $message = "All Control Forms has been signed";
                $this->success($message);
                \Log::info($message);
            }
        }
        catch(\Exception $ex){
            $this->error($ex->getMessage());
            \Log::error($ex->getMessage());
        }
    }
}

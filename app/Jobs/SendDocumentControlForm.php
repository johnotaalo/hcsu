<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

// Models
use App\Models\SubmissionControlForm;

// Helpers
use App\Helpers\HCSU\AdobeSign\AdobeClient;

class SendDocumentControlForm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $controlForm;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SubmissionControlForm $controlForm)
    {
        $this->controlForm = $controlForm;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("Starting control form procedure: " . $this->controlForm->id);
        try{
            \Log::info("Looking for document template id");
            $templateId = ((array)json_decode(\Storage::get("control-forms.json")))['documentId'];
            \Log::info("Template document retrieved successfully: " . $templateId);

            // Get tab data for submission control form
            \Log::info("Fetching tab data");
            $data = \App\Helpers\HCSU\PDFTK\SubmissionControlForm::getData($this->controlForm->id);
            \Log::debug("Tab Data: " . json_encode($data));

            // Sending document for signature
            $title = "{$data['case_no']} - Submission Control Form {$this->controlForm->application->APP_TITLE}";
            $recepients = $this->getClientEmails($data['email']);

            $agreement_id = AdobeClient::sendGenericDocument($templateId, $data, $title, $recepients);
            \Log::info("Successfully sent document for signature. Agreement Id: {$agreement_id}");

            // Save the agreementId to the database
            $this->controlForm->AGREEMENT_ID = $agreement_id;
            $this->controlForm->STATUS = "OUT_FOR_SIGNATURE";

            $this->controlForm->save();
        }catch(\Exception $ex){
            \Log::error("Exception met: " . $ex->getMessage());
        }

    }

    function getClientEmails($email = "N/A"){
        $emails = [];

        if ($email != "N/A"){
            $emails = ["email" => $email];
        }else{

        }
        return $emails;
    }
}

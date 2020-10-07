<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\HCSU\AdobeSign\AdobeClient;
use App\Helpers\HCSU\ProcessMaker;
use App\FormTemplate;

class CheckAdobeSignStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adobesign:checkstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This checks the status of all pending adobe sign documents and downloads any documents that may have been downloaded';

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
        $documents = \App\AdobeSignDocuments::where('AGREEMENT_STATUS', '!=', 'SIGNED')->orWhere('AGREEMENT_STATUS', '!=', 'ABORTED')->orWhere('AGREEMENT_STATUS', NULL)->get();

        if ($documents) {
            $message = "There are " . count($documents) . " document(s) yet to be signed and downloaded";
            $this->info($message);
            \Log::debug($message);

            foreach ($documents as $document) {
                $agreementDetails = AdobeClient::getAgreementDetails($document->AGREEMENT_ID);
                
                $document->AGREEMENT_STATUS = $agreementDetails->status;
                $document->PAYLOAD = json_encode($agreementDetails);
                $document->save();

                $case = \App\Models\PM\Application::where('APP_NUMBER', $document->CASE_NO)->first();
                if ($agreementDetails->status == "SIGNED" && $document->SIGNED_DOCUMENT_PATH == NULL) {
                    
                    \Log::debug("case: {$case->APP_UID}");
                    $template = FormTemplate::where('process', $case->PRO_UID)->first();

                    $this->info("Downloading signed document. Agreement ID: {$agreementDetails->agreementId}");
                    \Log::debug("Downloading signed document. Agreement ID: {$agreementDetails->agreementId}");

                    $documentContent = AdobeClient::downloadSignedDocument($agreementDetails->agreementId);

                    $path = "adobe-sign/documents/{$document->CASE_NO}-signed.pdf";

                    \Storage::put($path, $documentContent);

                    $this->info("Signed Document saved in: " . storage_path("app/" . $path));
                    \Log::debug("Signed Document saved in: " . storage_path("app/" . $path));

                    $response = ProcessMaker::uploadGeneratedForm($case->APP_UID, $template->task, $template->input_document, $path);
                    \Log::debug("ProcessMaker upload documents: " . json_encode($response));
                }else{
                    $this->info("Agreement ID: {$agreementDetails->agreementId} has not been signed yet");
                    \Log::debug("Agreement ID: {$agreementDetails->agreementId} has not been signed yet");
                    if($document->ROUTING){
                        $signingURLs = \App\Helpers\HCSU\AdobeSign\AdobeClient::getSigningURLs($document->AGREEMENT_ID);
                        \Log::debug("Testing Routing..." . json_encode($signingURLs));
                        $email = $signingURLs->signingUrlSetInfos[0]->signingUrls[0]->email;
                        \Log::debug("Next email: {$email}");
                        if (\App\Models\AdobeSignSignatory::where('email', $email)->exists()) {
                            \Log::debug("The next signer is a manager");
                            $this->info("Routing Case {$case->APP_NUMBER}");
                            \Log::debug("Routing Case {$case->APP_NUMBER}");
                            $response = ProcessMaker::routeCase($case->APP_UID);
                            \Log::debug("1. Case {$case->APP_NUMBER} response: " . json_encode($response));
                            $response = ProcessMaker::routeCase($case->APP_UID);
                            \Log::debug("2. Case {$case->APP_NUMBER} response: " . json_encode($response));
                            $document->ROUTING = 0;
                        }
                        $document->URLS = json_encode($signingURLs);
                    }
                    $document->save();
                }
            }
        }else{
            $message = "All documents have been signed";
            $this->success($message);
            \Log::debug($message);
        }
    }
}

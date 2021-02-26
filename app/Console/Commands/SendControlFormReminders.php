<?php

namespace App\Console\Commands;

use App\Helpers\HCSU\AdobeSign\AdobeClient;
use Illuminate\Console\Command;

use App\Models\SubmissionControlForm;

class SendControlFormReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adobesign:sendreminders {--type=control-forms}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to signatories';

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
            $type = $this->option("type");
            if ($type == "control-forms"){
                $controlForms = SubmissionControlForm::where('STATUS', '!=', 'SIGNED')->orWhere('STATUS', '!=', 'ABORTED')->get();
                if (count($controlForms) > 0){
                    $message = "There are " . count($controlForms) . " control form(s) yet to be signed";
                    $this->info($message);
                    \Log::info($message);
                    foreach ($controlForms as $form){
                        $agreementDetails = AdobeClient::getAgreementDetails($form->AGREEMENT_ID);
                        if ($agreementDetails->status == "OUT_FOR_SIGNATURE"){
                            $this->info("Sending reminder");
                            \Log::info("Sending reminder");
                            $res = AdobeClient::sendReminder($agreementDetails->agreementId);
                            \Log::debug("Agreement ID: {$agreementDetails->agreementId} has not been signed yet. Sending reminder");
                            if ($res->result && $res->result == "REMINDER_SENT"){
                                $this->info("Reminder sent successfully");
                                \Log::info("Reminder Sent Successfully");
                            }else{
                                $this->error("There was an error sending the reminder");
                                \Log::error("There was an error sending the reminder");
                                \Log::error($res->message);
                            }
                        }else{
                            $this->info("Agreement: {$agreementDetails->agreementId} status: {$agreementDetails->status}");
                            \Log::info("Agreement: {$agreementDetails->agreementId} status: {$agreementDetails->status}");
                        }
                    }
                }else{
                    $this->info("No reminders to send");
                    \Log::info("No reminders to send");
                }
            }
        }catch(\Exception $ex){
            $this->error($ex->getMessage());
            \Log::error($ex->getMessage());
        }
    }
}

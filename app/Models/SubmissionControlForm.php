<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionControlForm extends Model
{
    protected $connection = "pm_data";
    protected $table = "CONTROL_FORMS";
    protected $fillable = ["CASE_NO", "HOST_COUNTRY_ID", "DOCUMENTS", "STATUS", "AGREEMENT_ID", "USER_UID"];
    protected $appends = ["client", "application", "user", "process"];

    protected function getUserAttribute(){
        return \App\Models\PM\User::where("USR_UID", $this->USER_UID)->first();
    }

    protected function getApplicationAttribute(){
        return \App\Models\PM\Application::where("APP_NUMBER", $this->CASE_NO)->first();;
    }

    protected function getProcessAttribute(){
        $application = \App\Models\PM\Application::where("APP_NUMBER", $this->CASE_NO)->first();
        return $application->process;
    }

    protected function getClientAttribute(){
        $clientType = identify_hcsu_client($this->HOST_COUNTRY_ID);
        if ($clientType == "staff"){
            return \App\Models\Principal::where("HOST_COUNTRY_ID", $this->HOST_COUNTRY_ID)->first();
        }elseif($clientType == "dependent"){
            return \App\Models\PrincipalDependent::where("HOST_COUNTRY_ID", $this->HOST_COUNTRY_ID)->first();
        }elseif ($clientType == "agency"){
            return \App\Models\Agency::where("HOST_COUNTRY_ID", $this->HOST_COUNTRY_ID)->first();
        }elseif ($clientType == "domestic-worker"){
            return \App\Models\PrincipalDomesticWorker::where("HOST_COUNTRY_ID", $this->HOST_COUNTRY_ID)->first();
        }elseif ($clientType == "other-clients"){
            return \App\Models\OtherClient::where("HOST_COUNTRY_ID", $this->HOST_COUNTRY_ID)->first();
        }
    }
}

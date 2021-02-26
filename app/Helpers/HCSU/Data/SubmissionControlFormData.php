<?php


namespace App\Helpers\HCSU\Data;

use \App\Models\SubmissionControlForm;

class SubmissionControlFormData
{
    public static function get($id){
        $data = new \StdClass;
        $clientObj = new \StdClass;

        $controlForm = SubmissionControlForm::find($id);

        $clientType = identify_hcsu_client($controlForm->HOST_COUNTRY_ID);
        $clientObj->type = $clientType;

        $client = $controlForm->client;

        if ($clientObj->type == "staff") {
            $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$controlForm->HOST_COUNTRY_ID})"))->first();

            $mission = $contract->ACRONYM;
            $name = format_other_names($client->OTHER_NAMES) . " " . strtoupper($client->LAST_NAME);

            $client_name = $name;

            $clientObj->name = $client_name;
            $clientObj->organization = $mission;
            $clientObj->tel = $client->MOBILE_NO;
            $clientObj->email = $client->EMAIL;
        }elseif ($clientObj->type == "dependent"){
            $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$client->principal->HOST_COUNTRY_ID})"))->first();
            $mission = $contract->ACRONYM;

            $relationship = $client->relationship->RELATIONSHIP;
            $relationship = ($relationship == "Spouse") ? strtolower($relationship) : "dependent";

            $name = format_other_names($client->OTHER_NAMES) . " " . strtoupper($client->LAST_NAME) . "({$relationship})";

            $clientObj->name = $name;
            $clientObj->organization = $mission;
            $clientObj->tel = $client->principal->MOBILE_NO;
            $clientObj->email = $client->principal->EMAIL;
        }else if($clientObj->type == "agency"){
            $clientObj->name = $client->ACRONYM;
            $clientObj->organization = $client->ACRONYM;
            $clientObj->tel = "N/A";
            $clientObj->email = "N/A";
        }else if($clientObj->type == "domestic-worker"){
            $name = format_other_names($client->OTHER_NAMES) . " " . strtoupper($client->LAST_NAME) . "(Domestic Worker)";

            $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$client->principal->HOST_COUNTRY_ID})"))->first();

            $clientObj->name = $name;
            $clientObj->organization = $contract->ACRONYM;
            $clientObj->tel = $client->principal->MOBILE_NO;
            $clientObj->email = $client->principal->EMAIL;
        }

        $data->client = $clientObj;
        $data->data = $controlForm;
        $data->process = $controlForm->process->CON_VALUE;
        $data->case_no = $controlForm->CASE_NO;
        $data->date = date("F d, Y");

        return $data;
    }
}

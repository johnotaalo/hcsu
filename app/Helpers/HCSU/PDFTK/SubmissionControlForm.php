<?php


namespace App\Helpers\HCSU\PDFTK;


class SubmissionControlForm
{
    private $filename;


    public static function getData($id){
        $form_data = \App\Helpers\HCSU\Data\SubmissionControlFormData::get($id);

        $cleanedDocuments = [];
        $docs = json_decode($form_data->data->DOCUMENTS);

        $counter = 1;
        foreach ($docs as $doc){
            $cleanedDocuments["document_{$counter}"] = $doc;
            $counter++;
        }

        $tabData = [
            "name"              =>  $form_data->client->name,
            "tel"               =>  $form_data->client->tel,
            "organization"      =>  $form_data->client->organization,
            "email"             =>  $form_data->client->email,
            "case_no"           =>  $form_data->case_no,
            "date"              =>  $form_data->date,
            "purpose"           =>  $form_data->process,
            "submitted_by"      =>  $form_data->client->name,
            "received_by"       =>  strtoupper($form_data->data->user->USR_LASTNAME) . ", " . format_other_names($form_data->data->user->USR_FIRSTNAME),
            "submitted_date"    =>  date('d/m/Y'),
            "received_date"     =>  date("d/m/Y", strtotime($form_data->data->application->APP_CREATE_DATE)),
            "initials"          =>  $form_data->data->user->initials
        ];

        $tabData = $tabData + $cleanedDocuments;

        return $tabData;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }
}

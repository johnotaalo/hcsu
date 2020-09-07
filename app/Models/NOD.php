<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NOD extends Model
{
    protected $connection = 'pm_data';
    protected $table = "AC_03";

    protected $appends = ['diplomaticid_status'];

    public function getDiplomaticidStatusAttribute(){
        $diplomaticIds = $this->attributes['DIPLOMATIC_IDS'];
        $diplomaticIdsArray = json_decode($diplomaticIds);

        $diplomaticData = [];
        if($diplomaticIds){
            if ($diplomaticIdsArray) {
            	foreach ($diplomaticIdsArray as $key => $card) {
            		$diplomaticData[($card->dipIDStatus == 1) ? 'Returned' : 'Lost'][] = $card->diplomaticIDNo;
            	}
            }
        }

        return $diplomaticData;
    }
}

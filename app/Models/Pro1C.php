<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro1C extends Model
{
	protected $connection = "pm_data";
    protected $table = "DF_03";

    protected $appends = ['vehicle_excel_list'];

    public function vehicle(){
    	if ($this->VEHICLE_DATA_ORIGIN == "old") {
    		return $this->hasOne(\App\Models\OLDPM\StaffVehicle::class, "record_id", "VEHICLE_ID");
    	}
    }

    public function getVehicleExcelListAttribute(){
    	$vehicle = $this->vehicle;

    	$chassis_no = $vehicle->chassis_no;
    	$engine_no = $vehicle->engine_no;
    	$regt_no = $vehicle->regt_no;

    	$where_clause = "";
    	$queryBuilder = \DB::connection('dev_pm')->table('raw_data.MV_List');

    	if ($chassis_no && $engine_no) {
    		// $where_clause = "`CHASSIS NO` = '{$chassis_no}' AND `ENGINE NO` = '{$engine_no}'";
    		$queryBuilder = $queryBuilder->where('CHASSIS NO', $chassis_no)->where('ENGINE NO', $engine_no);
    	}elseif ($chassis_no && !$engine_no) {
    		// $where_clause = "`CHASSIS NO` = '{$chassis_no}'";
    		$queryBuilder = $queryBuilder->where('CHASSIS NO', $chassis_no);
    	}elseif (!$chassis_no && $engine_no) {
    		// $where_clause = "`ENGINE NO` = '{$engine_no}'";
    		$queryBuilder = $queryBuilder->where('ENGINE NO', $engine_no);
    	}elseif (!$chassis_no && !$engine_no && $regt_no) {
    		// $where_clause = "`REGISTRATION` = '{$regt_no}'";
    		$queryBuilder = $queryBuilder->where('REGISTRATION', $regt_no);
    	}else{
    		return new \StdClass;
    	}

    	// $sql = "SELECT * FROM raw_data.`MV_List` WHERE {$where_clause} LIMIT 1";
    	// $data = \DB::connection('dev_pm')->select($sql);
    	$queryData = $queryBuilder->first();

    	return $queryData;
    }
}

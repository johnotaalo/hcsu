<?php

use Illuminate\Database\Seeder;

class UpdatePMAbbreviationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = \App\Models\OLDPM\Country::all();

        foreach ($countries as $country) {
        	$newCountry = \App\Models\Country::where('iso_2', $country->cnty_iso_id_code)->first();
        	if($newCountry){
        		$newCountry->pm_abbrev = $country->cnty_abbrv_name;
        		$newCountry->save();
        	}
        }
    }
}

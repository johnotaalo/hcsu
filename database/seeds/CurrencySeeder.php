<?php

use Illuminate\Database\Seeder;

use App\Models\Ref\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currenciesJson = \File::get(public_path('currencies.json'));
        $currenciesArray = json_decode($currenciesJson);

        $cleanedArray = collect($currenciesArray)->map(function($currency){
        	return [
        		'CODE'			=>	$currency->code,
        		'NAME'			=>	$currency->name,
        		'SYMBOL'		=>	$currency->symbol,
        		'created_at'	=>	date('Y-m-d H:i:s'),
        		'updated_at'	=>	date('Y-m-d H:i:s')
        	];
        })->toArray();

        Currency::truncate();
        Currency::insert($cleanedArray);
    }
}

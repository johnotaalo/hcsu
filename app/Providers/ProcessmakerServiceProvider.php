<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ProcessmakerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path() . '/Helpers/HCSU/ProcessMaker.php';
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FocalPointPasswordBrokerManager;

class FocalPointPasswordResetServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPasswordBrokerManager();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('auth.password', function ($app) {
            return new CustomPasswordBrokerManager($app);
        });
    }

    public function provides()
    {
        return ['auth.password'];
    }
}

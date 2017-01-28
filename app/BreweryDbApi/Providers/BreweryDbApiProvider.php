<?php

namespace App\BreweryDbApi\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use App\BreweryDbApi\BreweryDbApi as BreweryDb;
use App\BreweryDbApi\BreweryDbCounter as Counter;

class BreweryDbApiProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        AliasLoader::getInstance()->alias('BreweryDbApi', 'App\BreweryDbApi\BreweryDbApi');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('BreweryDbApi', function($app) {
            $bdb = new BreweryDb($app['config']['brewerydb']);
            $bdb->setCounter(new Counter());
            return $bdb;
        });
    }
//    public function register()
//    {
//        $this->app->singleton('BreweryDbApi', function ($app) {
//            return new BreweryDbApi($app['config']['brewerydb']);
//        });
//        $this->app->booting(function () {
//            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//            $loader->alias('BreweryDbApi', 'App\BreweryDbApi\Facades\BreweryDbApi');
//        });
//    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['BreweryDbApi', 'App\BreweryDbApi\BreweryDbApi'];
    }
}

<?php

namespace Arifhp86\ClearExpiredCacheFile\Providers;

use Arifhp86\ClearExpiredCacheFile\Console\ClearExpiredCommand;
use Illuminate\Support\ServiceProvider;

class CacheClearServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    // protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        
    }
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClearExpiredCommand::class,
            ]);
        }
    }
}

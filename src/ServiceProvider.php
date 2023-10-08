<?php

namespace Arifhp86\ClearExpiredCacheFile;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(CacheGarbageCollector::class)
            ->needs(Filesystem::class)
            ->give(function () {
                config(['filesystems.disks.cache-folder' => [
                    'driver' => 'local',
                    'root' => config('cache.stores.file.path'),
                ]]);

                return Storage::disk('cache-folder');
            });
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

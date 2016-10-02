<?php

namespace App\Providers;

use App\Services\PiliService;
use Illuminate\Support\ServiceProvider;

class PiliSerivceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\PiliService', function () {
            return new PiliService(
                env('PILI_ACCESSKEY'),
                env('PILI_SECRETKEY'),
                env('PILI_HUBNAME'),
                env('RTMP_PUBLISH_URL'),
                env('RTMP_PLAY_URL'),
                env('HLS_PLAY_URL'),
                env('HDL_PLAY_URL'),
                env('SNAPSHOT_PLAY_URL')
            );
        });
    }
}

<?php

namespace ElfSundae\Laravel\MultiApp;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class MultiAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }

    /**
     * Publish assets from package.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            __DIR__.'/../config/multi-app.php' => base_path('config/multi-app.php'),
        ], 'laravel-multi-app');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->setupAssets();
    }

    /**
     * Setup package assets.
     *
     * @return void
     */
    protected function setupAssets()
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('multi-app');
        }

        $this->mergeConfigFrom(__DIR__.'/../config/multi-app.php', 'multi-app');
    }
}

<?php

namespace ElfSundae\Laravel\Apps;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class AppsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->macroUrlGenerator();

        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }

    /**
     * Register macros for UrlGenerator.
     *
     * @return void
     */
    protected function macroUrlGenerator()
    {
        $this->app['url']::macro('getRootControllerNamespace', function () {
            return $this->rootNamespace;
        });
    }

    /**
     * Publish assets from package.
     *
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            __DIR__.'/../config/apps.php' => base_path('config/apps.php'),
        ], 'laravel-apps');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->setupAssets();

        $this->app->singleton('apps', function ($app) {
            return new Apps($app);
        });
        $this->app->alias('apps', Apps::class);
    }

    /**
     * Setup package assets.
     *
     * @return void
     */
    protected function setupAssets()
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('apps'); // @codeCoverageIgnore
        }

        $this->mergeConfigFrom(__DIR__.'/../config/apps.php', 'apps');
    }
}

<?php

namespace ElfSundae\Laravel\Apps;

use Illuminate\Support\ServiceProvider;

class AppsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMacros();

        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }

    /**
     * Register macros.
     *
     * @return void
     */
    protected function registerMacros()
    {
        $this->app['url']->macro('getRootControllerNamespace', function () {
            /* @var $this \Illuminate\Routing\UrlGenerator */
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
            return new AppManager($app);
        });
        $this->app->alias('apps', AppManager::class);
    }

    /**
     * Setup package assets.
     *
     * @return void
     */
    protected function setupAssets()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/apps.php', 'apps');
    }
}

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

        if (! $this->app['config']->has('apps.domain')) {
            $this->app['config']['apps.domain'] = array_map(function ($url) {
                return parse_url($url, PHP_URL_HOST);
            }, $this->app['config']['apps.url']);
        }
    }
}

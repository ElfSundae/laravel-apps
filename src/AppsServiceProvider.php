<?php

namespace ElfSundae\Apps;

use Illuminate\Support\Arr;
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
        (new MacroRegistrar)->registerMacros($this->app);

        $this->publishAssets();
    }

    /**
     * Publish assets from package.
     *
     * @return void
     */
    protected function publishAssets()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/apps.php' => config_path('apps.php'),
            ], 'laravel-apps');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->setupAssets();

        $this->registerAppManager();

        $this->registerConfiguredProviders();

        $this->setupConfiguration();
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

    /**
     * Register app manager singleton.
     *
     * @return void
     */
    protected function registerAppManager()
    {
        $this->app->singleton('apps', function ($app) {
            return new AppManager($app);
        });

        $this->app->alias('apps', AppManager::class);
    }

    /**
     * Register the configured service providers.
     *
     * @return void
     */
    protected function registerConfiguredProviders()
    {
        $providers = $this->app['config']->get('apps.providers', []);

        if ($this->app->runningInConsole()) {
            $providers = array_unique(Arr::flatten($providers));
        } else {
            $providers = (array) Arr::get($providers, $this->app['apps']->id());
        }

        array_walk($providers, function ($p) {
            $this->app->register($p);
        });
    }

    /**
     * Setup application configurations.
     *
     * @return void
     */
    protected function setupConfiguration()
    {
        $booting = $this->app->isBooted() ? 'booted' : 'booting';

        $this->app->$booting(function ($app) {
            $config = $app['config'];

            if (! $app->configurationIsCached()) {
                $config->set($config->get('apps.config.default', []));
            }

            if ($appId = $app['apps']->id()) {
                $config->set($config->get('apps.config.'.$appId, []));
            }
        });
    }
}

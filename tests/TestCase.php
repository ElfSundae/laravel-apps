<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function registerApps($config = [])
    {
        $this->app['config']['apps'] = $config;
        $this->app->register(AppsServiceProvider::class);
    }

    protected function setRequestUrl($url)
    {
        $this->app['config']['app.url'] = $url;
        $this->app['Illuminate\Foundation\Bootstrap\SetRequestForConsole']->bootstrap($this->app);
    }
}

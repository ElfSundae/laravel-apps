<?php

namespace ElfSundae\Laravel\Apps\Test;

use Orchestra\Testbench\TestCase;
use ElfSundae\Laravel\Apps\AppsServiceProvider;

class HelpersTest extends TestCase
{
    public function test_is_app()
    {
        $this->registerAppsWithConfig([
            'url' => [
                'web' => 'http://example.com',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
            ],
        ]);

        $this->setRequestUrl('http://example.com');
        $this->assertTrue(is_app('web'));
        $this->assertFalse(is_app('admin'));
        $this->assertFalse(is_app('api'));
        $this->assertFalse(is_app('none'));
        $this->assertTrue(is_app('none', 'web'));

        $this->setRequestUrl('https://example.com/api');
        $this->assertTrue(is_app('api'));
        $this->setRequestUrl('http://example.com/api?foo');
        $this->assertTrue(is_app('api'));
        $this->setRequestUrl('http://example.com/api/foo');
        $this->assertTrue(is_app('api'));
    }

    public function test_app_url()
    {
        $this->registerAppsWithConfig([
            'url' => [
                'web' => 'http://example.com',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
            ],
        ]);
        $this->app['config']['app.url'] = 'http://example.com';

        $this->assertSame('http://example.com', app_url());
        $this->assertSame('http://example.com', app_url('/'));
        $this->assertSame('http://example.com/path', app_url('path'));
        $this->assertSame('http://example.com/path?foo', app_url('path?foo'));
        $this->assertSame('http://example.com/path?foo&bar=bar+value', app_url('path?foo', ['bar' => 'bar value']));
        $this->assertSame('http://admin.example.com/path?foo&bar=value', app_url('path?foo', ['bar' => 'value'], 'admin'));
        $this->assertSame('http://admin.example.com/path?foo&bar=value', app_url('path?foo', 'admin', ['bar' => 'value']));
        $this->assertSame('http://example.com/api', app_url('', 'api'));
    }

    protected function setRequestUrl($url)
    {
        $this->app['config']['app.url'] = $url;
        $this->app['Illuminate\Foundation\Bootstrap\SetRequestForConsole']->bootstrap($this->app);
    }

    protected function registerAppsWithConfig($config = [])
    {
        $this->app['config']['apps'] = $config;
        $this->app->register(AppsServiceProvider::class);
    }
}

<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\Apps;

class AppsTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(Apps::class, $this->getApps());
    }

    public function testGetId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
                'web' => 'http://example.com',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame('testing', $apps->id());

        $this->setRequestUrl('http://example.com/foo');
        $this->assertSame('web', $apps->id());

        $this->setRequestUrl('http://foo.app');
        $this->assertNull($apps->id());
    }

    public function testCheckId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
            ],
        ]);
        $apps = $this->getApps();

        $this->assertTrue($apps->id('testing'));
        $this->assertFalse($apps->id('foo'));
        $this->assertTrue($apps->id('foo', 'testing'));
        $this->assertTrue($apps->id(['foo', 'testing']));
    }

    public function testRefreshId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame($apps, $apps->refreshId());
        $this->assertSame('testing', $apps->refreshId()->id());
    }

    public function testGetIdForUrl()
    {
        $this->setAppsConfig([
            'url' => [
                'web' => 'http://example.com',
                'dev' => 'http://example.com:8080',
                'mirror' => 'http://example.com.cn',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
                'api_v2' => 'http://example.com/api/v2',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertNull($apps->idForUrl('http://foo.bar'));
        $this->assertSame('web', $apps->idForUrl('http://example.com'));
        $this->assertSame('web', $apps->idForUrl('https://example.com'));
        $this->assertSame('dev', $apps->idForUrl('http://example.com:8080'));
        $this->assertSame('dev', $apps->idForUrl('http://example.com:8080/api'));
        $this->assertSame('mirror', $apps->idForUrl('http://example.com.cn'));
        $this->assertSame('mirror', $apps->idForUrl('http://example.com.cn/api'));
        $this->assertSame('admin', $apps->idForUrl('http://admin.example.com'));
        $this->assertSame('admin', $apps->idForUrl('http://admin.example.com/api'));

        $this->assertSame('api', $apps->idForUrl('http://example.com/api'));
        $this->assertSame('api', $apps->idForUrl('https://example.com/api/'));
        $this->assertSame('api', $apps->idForUrl('https://example.com/api/foo'));
        $this->assertSame('api', $apps->idForUrl('https://example.com/api?foo'));
        $this->assertSame('api', $apps->idForUrl('https://example.com/api#foo'));
        $this->assertSame('web', $apps->idForUrl('https://example.com/apifoo'));
        $this->assertSame('api_v2', $apps->idForUrl('http://example.com/api/v2/foo'));
        $this->assertSame('api_v2', $apps->idForUrl('HTTPS://EXAMPLE.COM/API/V2/FOO'));
    }

    public function testGetRootUrl()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://example.com/api',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame('http://example.com', $apps->root());
        $this->assertSame('http://example.com', $apps->root(null));
        $this->assertSame('http://example.com', $apps->root('foo'));
        $this->assertSame('http://example.com/api', $apps->root('api'));
    }

    public function testGetDomain()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://api.example.com/v1',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame('example.com', $apps->domain());
        $this->assertSame('api.example.com', $apps->domain('api'));
    }

    public function testGetPrefix()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://api.example.com/v1',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame('', $apps->prefix());
        $this->assertSame('v1', $apps->prefix('api'));
    }

    public function testGenerateUrl()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://example.com/api',
            ],
        ]);
        $apps = $this->getApps();

        $this->assertSame('http://example.com', $apps->url());
        $this->assertSame('http://example.com', $apps->url(null));
        $this->assertSame('http://example.com', $apps->url('web'));
        $this->assertSame('http://example.com/api', $apps->url('api'));
        $this->assertSame(
            'http://example.com/api/path/foo/bar?query=value',
            $apps->url('api', 'path?query=value', ['foo', 'bar'])
        );
    }

    protected function getApps()
    {
        return new Apps($this->app);
    }
}

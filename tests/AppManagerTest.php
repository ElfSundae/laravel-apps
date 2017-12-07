<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppManager;

class AppManagerTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(AppManager::class, $this->getManager());
    }

    public function testMacroable()
    {
        AppManager::macro('fooMethod', function ($arg) {
            return $arg;
        });

        $this->assertSame('foo', $this->getManager()->fooMethod('foo'));
    }

    public function testGetAllAppUrls()
    {
        $urls = [
            'web' => 'http://example.com',
            'admin' => 'http://admin.example.com',
        ];
        $this->setAppsConfig(['url' => $urls]);
        $this->assertEquals($urls, $this->getManager()->appUrls());
    }

    public function testGetAppUrl()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://example.com/api',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('http://example.com', $manager->appUrl());
        $this->assertSame('http://example.com', $manager->appUrl(null));
        $this->assertSame('http://example.com', $manager->appUrl('foo'));
        $this->assertSame('http://example.com/api', $manager->appUrl('api'));
    }

    public function testGetRootUrl()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://example.com/api',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('http://example.com', $manager->root());
        $this->assertSame('http://example.com', $manager->root(null));
        $this->assertSame('http://example.com', $manager->root('foo'));
        $this->assertSame('http://example.com/api', $manager->root('api'));
    }

    public function testGetDomain()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://api.example.com/v1',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('example.com', $manager->domain());
        $this->assertSame('api.example.com', $manager->domain('api'));
    }

    public function testGetPrefix()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'http://api.example.com/foo/bar',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('', $manager->prefix());
        $this->assertSame('foo/bar', $manager->prefix('api'));
    }

    public function testGetId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
                'web' => 'http://example.com',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('testing', $manager->id());

        $this->setRequestUrl('http://example.com/foo');
        $this->assertSame('web', $manager->id());

        $this->setRequestUrl('http://foo.app');
        $this->assertNull($manager->id());
    }

    public function testCheckId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
            ],
        ]);
        $manager = $this->getManager();

        $this->assertTrue($manager->id('testing'));
        $this->assertFalse($manager->id('foo'));
        $this->assertTrue($manager->id('foo', 'testing'));
        $this->assertTrue($manager->id(['foo', 'testing']));
    }

    public function testRefreshId()
    {
        $this->setAppsConfig([
            'url' => [
                'testing' => $this->app['request']->root(),
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame($manager, $manager->refreshId());
        $this->assertSame('testing', $manager->refreshId()->id());
    }

    public function testGetAppIdForUrl()
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
        $manager = $this->getManager();

        $this->assertNull($manager->appIdForUrl('http://foo.bar'));
        $this->assertSame('web', $manager->appIdForUrl('http://example.com'));
        $this->assertSame('web', $manager->appIdForUrl('https://example.com'));
        $this->assertSame('dev', $manager->appIdForUrl('http://example.com:8080'));
        $this->assertSame('dev', $manager->appIdForUrl('http://example.com:8080/api'));
        $this->assertSame('mirror', $manager->appIdForUrl('http://example.com.cn'));
        $this->assertSame('mirror', $manager->appIdForUrl('http://example.com.cn/api'));
        $this->assertSame('admin', $manager->appIdForUrl('http://admin.example.com'));
        $this->assertSame('admin', $manager->appIdForUrl('http://admin.example.com/api'));

        $this->assertSame('api', $manager->appIdForUrl('http://example.com/api'));
        $this->assertSame('api', $manager->appIdForUrl('https://example.com/api/'));
        $this->assertSame('api', $manager->appIdForUrl('https://example.com/api/foo'));
        $this->assertSame('api', $manager->appIdForUrl('https://example.com/api?foo'));
        $this->assertSame('api', $manager->appIdForUrl('https://example.com/api#foo'));
        $this->assertSame('web', $manager->appIdForUrl('https://example.com/apifoo'));
        $this->assertSame('api_v2', $manager->appIdForUrl('http://example.com/api/v2/foo'));
        $this->assertSame('api_v2', $manager->appIdForUrl('HTTPS://EXAMPLE.COM/API/V2/FOO'));
    }

    public function testGenerateUrl()
    {
        $this->app['config']->set([
            'app.url' => 'http://example.com',
            'apps.url' => [
                'api' => 'https://api.example.com/v1',
            ],
        ]);
        $manager = $this->getManager();

        $this->assertSame('http://example.com', $manager->url());
        $this->assertSame('http://example.com', $manager->url(null));
        $this->assertSame('http://example.com', $manager->url('web'));
        $this->assertSame('https://api.example.com/v1', $manager->url('api'));
        $this->assertSame('https://api.example.com/v1/path/foo/bar', $manager->url('api', 'path', ['foo', 'bar']));
    }

    public function testRegisteredMacros()
    {
        AppManager::registerMacros($this->app);

        $this->assertTrue($this->app['url']->hasMacro('getRootControllerNamespace'));
        $this->app['url']->setRootControllerNamespace('Foo');
        $this->assertSame('Foo', $this->app['url']->getRootControllerNamespace());
    }

    protected function getManager()
    {
        return new AppManager($this->app);
    }
}

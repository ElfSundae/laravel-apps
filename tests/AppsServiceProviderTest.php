<?php

namespace ElfSundae\Apps\Test;

use Mockery as m;
use Illuminate\Http\Request;
use ElfSundae\Apps\AppManager;
use ElfSundae\Apps\Facades\Apps;
use Illuminate\Foundation\Application;
use ElfSundae\Apps\AppsServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppsServiceProviderTest extends TestCase
{
    public function tearDown(): void
    {
        $this->app['files']->delete(config_path('apps.php'));

        parent::tearDown();
    }

    public function testRegisteredAppManager()
    {
        $this->registerAppsService();
        $manager = $this->app['apps'];
        $this->assertInstanceOf(AppManager::class, $manager);
        $this->assertSame($manager, $this->app[AppManager::class]);
        $this->assertSame($manager, Apps::getFacadeRoot());
    }

    public function testRegisteredMacros()
    {
        $this->registerAppsService();
        $this->assertTrue($this->app['url']->hasMacro('getRootControllerNamespace'));
    }

    public function testPublishAssets()
    {
        $this->registerAppsService();
        $this->artisan('vendor:publish', [
            '--force' => true,
            '--provider' => AppsServiceProvider::class,
        ]);
        $this->assertFileExists(config_path('apps.php'));
    }

    public function testRegisteredAllConfiguredProvidersForConsole()
    {
        $this->registerAppsService([
            'url' => [
                'web' => 'http://localhost',
                'admin' => 'http://localhost/admin',
                'api' => 'http://localhost/api',
            ],
            'providers' => [
                'admin' => [
                    AdminServiceProvider::class,
                ],
                'api' => ApiServiceProvider::class,
            ],
        ]);
        $this->assertInstanceOf(AdminServiceProvider::class, $this->app->getProvider(AdminServiceProvider::class));
        $this->assertInstanceOf(ApiServiceProvider::class, $this->app->getProvider(ApiServiceProvider::class));
    }

    public function testNotRegisterConfiguredProviders()
    {
        $app = m::mock(Application::class)->makePartial();
        $app->shouldReceive('runningInConsole')->andReturn(false);
        $app->instance('config', $this->app['config']);
        $app->instance('events', $this->app['events']);
        $app->instance('request', $this->app['request']);
        $app['config']['apps'] = [
            'url' => [
                'web' => 'http://localhost',
                'admin' => 'http://localhost/admin',
                'api' => 'http://localhost/api',
            ],
            'providers' => [
                'admin' => [
                    AdminServiceProvider::class,
                ],
                'api' => ApiServiceProvider::class,
            ],
        ];

        $app->register(AppsServiceProvider::class);

        $this->assertNull($app->getProvider(AdminServiceProvider::class));
        $this->assertNull($app->getProvider(ApiServiceProvider::class));
    }

    public function testRegisteredConfiguredProvidersForCertainApp()
    {
        $app = m::mock(Application::class)->makePartial();
        $app->shouldReceive('runningInConsole')->andReturn(false);
        $app->instance('config', $this->app['config']);
        $app->instance('events', $this->app['events']);
        $app->instance('request', Request::create('http://localhost/admin'));
        $app['config']['apps'] = [
            'url' => [
                'web' => 'http://localhost',
                'admin' => 'http://localhost/admin',
                'api' => 'http://localhost/api',
            ],
            'providers' => [
                'admin' => [
                    AdminServiceProvider::class,
                ],
                'api' => ApiServiceProvider::class,
            ],
        ];

        $app->register(AppsServiceProvider::class);

        $this->assertInstanceOf(AdminServiceProvider::class, $app->getProvider(AdminServiceProvider::class));
        $this->assertNull($app->getProvider(ApiServiceProvider::class));
    }

    public function testSetupConfiguration()
    {
        $this->app['config']->set([
            'foo' => [
                'a' => '1',
                'b' => [
                    'c' => '2',
                    'd' => '3',
                ],
            ],
        ]);
        $this->registerAppsService([
            'url' => [
                'web' => 'http://localhost',
            ],
            'config' => [
                'default' => [
                    'foo.a' => 'v1',
                    'foo.b.c' => 'v2',
                ],
                'web' => [
                    'foo.b.d' => 'v3',
                ],
            ],
        ]);

        $this->assertEquals([
            'a' => 'v1',
            'b' => [
                'c' => 'v2',
                'd' => 'v3',
            ],
        ], $this->app['config']['foo']);
    }
}

class AdminServiceProvider extends ServiceProvider
{
    public function register()
    {
    }
}

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
    }
}

<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppManager;
use ElfSundae\Laravel\Apps\Facades\Apps;

class AppsServiceProviderTest extends TestCase
{
    public function tearDown()
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
            '--provider' => 'ElfSundae\Laravel\Apps\AppsServiceProvider',
        ]);
        $this->assertFileExists(config_path('apps.php'));
    }

    public function atestRegisteredConfiguredProviders()
    {
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

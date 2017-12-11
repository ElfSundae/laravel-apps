<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppManager;
use ElfSundae\Laravel\Apps\Facades\Apps;
use ElfSundae\Laravel\Apps\AppsServiceProvider;

class AppsServiceProviderTest extends TestCase
{
    public function tearDown()
    {
        $this->app['files']->delete(config_path('apps.php'));

        parent::tearDown();
    }

    public function testServiceProvider()
    {
        $manager = $this->app['apps'];

        $this->assertInstanceOf(AppManager::class, $manager);
        $this->assertSame($manager, $this->app[AppManager::class]);
        $this->assertSame($manager, Apps::getFacadeRoot());

        $this->artisan('vendor:publish', [
            '--force' => true,
            '--provider' => 'ElfSundae\Laravel\Apps\AppsServiceProvider',
        ]);
        $this->assertFileExists(config_path('apps.php'));

        $this->assertTrue($this->app['url']->hasMacro('getRootControllerNamespace'));

        $this->assertEquals([
            'a' => 'v1',
            'b' => [
                'c' => 'v2',
                'd' => 'v3',
            ],
        ], $this->app['config']['foo']);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set([
            'foo' => [
                'a' => '1',
                'b' => [
                    'c' => '2',
                    'd' => '3',
                ],
            ],
            'apps' => [
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
            ],
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [AppsServiceProvider::class];
    }
}

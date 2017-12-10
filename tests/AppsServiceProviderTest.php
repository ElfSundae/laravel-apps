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

    public function testServiceProvider()
    {
        $this->registerAppsService();

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
    }
}

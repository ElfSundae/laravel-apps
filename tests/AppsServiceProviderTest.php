<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppManager;
use ElfSundae\Laravel\Apps\Facades\Apps;

class AppsServiceProviderTest extends TestCase
{
    public function testBindings()
    {
        $this->registerAppsService();
        $manager = $this->app['apps'];
        $this->assertInstanceOf(AppManager::class, $manager);
        $this->assertSame($manager, $this->app[AppManager::class]);
        $this->assertSame($manager, Apps::getFacadeRoot());
    }

    public function testRegisteredUrlGeneratorMacros()
    {
        $this->registerAppsService();
        $url = $this->app['url'];
        $this->assertTrue($url->hasMacro('getRootControllerNamespace'));
        $url->setRootControllerNamespace('App');
        $this->assertSame('App', $url->getRootControllerNamespace());
    }
}

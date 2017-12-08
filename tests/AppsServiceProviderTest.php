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

    public function testRegisteredMacros()
    {
        $this->registerAppsService();
        $this->app['url']->setRootControllerNamespace('Foo');
        $this->assertSame('Foo', $this->app['url']->getRootControllerNamespace());
    }
}

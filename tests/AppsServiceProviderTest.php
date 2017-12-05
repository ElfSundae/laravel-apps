<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\Apps;
use ElfSundae\Laravel\Apps\Facades\Apps as AppsFacade;

class AppsServiceProviderTest extends TestCase
{
    public function testBindings()
    {
        $this->registerAppsService();
        $apps = $this->app['apps'];
        $this->assertInstanceOf(Apps::class, $apps);
        $this->assertSame($apps, $this->app[Apps::class]);
        $this->assertSame($apps, AppsFacade::getFacadeRoot());
    }

    public function testRegisteredUrlGeneratorMacros()
    {
        $this->registerAppsService();
        $url = $this->app['url'];
        $this->assertTrue($url::hasMacro('getRootControllerNamespace'));
        $url->setRootControllerNamespace('App');
        $this->assertSame('App', $url->getRootControllerNamespace());
    }
}

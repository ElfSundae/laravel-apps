<?php

namespace ElfSundae\Laravel\Apps\Test;

class HelpersTest extends TestCase
{
    public function test_apps()
    {
        $this->registerAppsService();
        $this->assertSame($this->app['apps'], apps());
    }
}

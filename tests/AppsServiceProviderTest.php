<?php

namespace ElfSundae\Laravel\Apps\Test;

use Orchestra\Testbench\TestCase;
use ElfSundae\Laravel\Apps\AppsServiceProvider;

class AppsServiceProviderTest extends TestCase
{
    public function testConfiguredDomains()
    {
        $this->app['config']['apps.url'] = [
            'web' => 'http://web.app',
            'api' => 'https://example.domain.app/api',
        ];
        $this->app->register(AppsServiceProvider::class);

        $this->assertEquals([
            'web' => 'web.app',
            'api' => 'example.domain.app',
        ], $this->app['config']['apps.domain']);
    }
}

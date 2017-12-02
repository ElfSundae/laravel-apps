<?php

namespace ElfSundae\Laravel\Apps\Test;

class AppsServiceProviderTest extends TestCase
{
    public function testConfiguredDomains()
    {
        $this->registerApps([
            'url' => [
                'web' => 'http://example.com',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
            ],
        ]);

        $this->assertEquals([
            'web' => 'example.com',
            'admin' => 'admin.example.com',
            'api' => 'example.com',
        ], $this->app['config']['apps.domain']);
    }
}

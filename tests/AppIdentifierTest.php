<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppIdentifier;

class AppIdentifierTest extends TestCase
{
    public function testGetForUrl()
    {
        $this->app['config']['apps'] = [
            'url' => [
                'web' => 'http://example.com',
                'dev' => 'http://example.com:8080',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
                'api_v2' => 'http://example.com/api/v2',
            ],
        ];

        $this->assertNull(AppIdentifier::getForUrl('http://foo.bar'));
        $this->assertSame('web', AppIdentifier::getForUrl('http://example.com'));

        $this->assertSame('dev', AppIdentifier::getForUrl('http://example.com:8080'));
        $this->assertSame('dev', AppIdentifier::getForUrl('http://example.com:8080/foo'));

        $this->assertSame('admin', AppIdentifier::getForUrl('http://admin.example.com'));
        $this->assertSame('admin', AppIdentifier::getForUrl('https://admin.example.com'));
        $this->assertSame('admin', AppIdentifier::getForUrl('http://admin.example.com/'));
        $this->assertSame('admin', AppIdentifier::getForUrl('http://admin.example.com/foo/bar'));
        $this->assertSame('admin', AppIdentifier::getForUrl('http://admin.example.com?foo=bar'));

        $this->assertSame('api', AppIdentifier::getForUrl('http://example.com/api'));
        $this->assertSame('web', AppIdentifier::getForUrl('http://example.com/apifoo'));
        $this->assertSame('api_v2', AppIdentifier::getForUrl('http://example.com/api/v2/foo'));
    }

    public function testGet()
    {
        app()->instance(AppIdentifier::IDENTIFIER_KEY, 'foo');
        $this->assertSame('foo', AppIdentifier::get());

        $this->registerApps([
            'url' => [
                'web' => 'http://example.com',
            ],
        ]);

        $this->setRequestUrl('http://example.com/foo');
        $this->assertSame('web', AppIdentifier::get());
        $this->assertSame('web', app(AppIdentifier::IDENTIFIER_KEY));

        $this->setRequestUrl('http://foo.app');
        $this->assertNull(AppIdentifier::get());
    }
}

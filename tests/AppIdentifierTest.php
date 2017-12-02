<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppIdentifier;

class AppIdentifierTest extends TestCase
{
    public function testGetIdentifier()
    {
        $this->registerApps([
            'url' => [
                'web' => 'http://example.com',
                'dev' => 'http://example.com:8080',
                'admin' => 'http://admin.example.com',
                'api' => 'http://example.com/api',
                'api_v2' => 'http://example.com/api/v2',
            ],
        ]);

        $this->setRequestUrl('http://example.com');
        $this->assertSame('web', AppIdentifier::get());
        $this->assertSame('web', app(AppIdentifier::IDENTIFIER_KEY));

        $this->setRequestUrl('http://example.com:8080');
        $this->assertSame('dev', AppIdentifier::get());
        $this->assertSame('dev', app(AppIdentifier::IDENTIFIER_KEY));

        $this->setRequestUrl('http://example.com:8080/foo');
        $this->assertSame('dev', AppIdentifier::get());

        $this->setRequestUrl('http://admin.example.com');
        $this->assertSame('admin', AppIdentifier::get());

        $this->setRequestUrl('https://admin.example.com');
        $this->assertSame('admin', AppIdentifier::get());

        $this->setRequestUrl('http://admin.example.com/');
        $this->assertSame('admin', AppIdentifier::get());

        $this->setRequestUrl('http://admin.example.com/foo/bar');
        $this->assertSame('admin', AppIdentifier::get());

        $this->setRequestUrl('http://admin.example.com?foo=bar');
        $this->assertSame('admin', AppIdentifier::get());

        $this->setRequestUrl('http://example.com/api');
        $this->assertSame('api', AppIdentifier::get());

        $this->setRequestUrl('http://example.com/apifoo');
        $this->assertSame('web', AppIdentifier::get());

        $this->setRequestUrl('http://example.com/api/v2/foo');
        $this->assertSame('api_v2', AppIdentifier::get());

        $this->setRequestUrl('http://foo.bar');
        $this->assertNull(AppIdentifier::get());
    }

    public function testGetExistingIdentifier()
    {
        app()->instance(AppIdentifier::IDENTIFIER_KEY, 'foo');
        $this->assertSame('foo', AppIdentifier::get());
    }
}

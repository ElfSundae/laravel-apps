<?php

namespace ElfSundae\Laravel\Apps\Test;

use ElfSundae\Laravel\Apps\AppIdentifier;

class HelpersTest extends TestCase
{
    public function test_app_id()
    {
        app()->instance(AppIdentifier::IDENTIFIER_KEY, 'foo');

        $this->assertSame('foo', app_id());
        $this->assertTrue(app_id('foo'));
        $this->assertFalse(app_id('bar'));
        $this->assertTrue(app_id('foo', 'bar'));
        $this->assertTrue(app_id(['foo', 'bar']));
    }

    public function test_app_url()
    {
        $this->app['config']->set([
            'app.url' => 'http://localhost',
            'apps.url' => [
                'api' => 'http://localhost/api',
            ],
        ]);

        $this->assertSame('http://localhost', app_url());
        $this->assertSame('http://localhost', app_url('/'));
        $this->assertSame('http://localhost/path', app_url('path'));
        $this->assertSame('http://localhost/path', app_url('/path'));
        $this->assertSame('http://localhost?foo', app_url('?foo'));
        $this->assertSame('http://localhost?foo', app_url('/?foo'));
        $this->assertSame('http://localhost?foo=bar', app_url('/', ['foo' => 'bar']));
        $this->assertSame('http://localhost/path?foo&key=a%20b', app_url('path?foo', ['key' => 'a b']));

        $this->assertSame('http://localhost/api', app_url('', 'api'));
        $this->assertSame('http://localhost/api', app_url('/', 'api'));
        $this->assertSame('http://localhost/api/path', app_url('path', 'api'));
        $this->assertSame('http://localhost/api?foo', app_url('/?foo', 'api'));
        $this->assertSame('http://localhost/api?foo&key=a%20b', app_url('?foo', 'api', ['key' => 'a b']));
        $this->assertSame('http://localhost/api/path?foo=bar', app_url('path', ['foo' => 'bar'], 'api'));
    }
}

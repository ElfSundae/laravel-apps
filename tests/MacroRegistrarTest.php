<?php

namespace ElfSundae\Apps\Test;

use ElfSundae\Apps\MacroRegistrar;
use Illuminate\Support\Traits\Macroable;

class MacroRegistrarTest extends TestCase
{
    public function testRegister()
    {
        $reg = new MacroRegistrar;

        $reg->register(Testing::class, 'foo', function ($arg) {
            return $arg;
        });
        $this->assertSame('abc', (new Testing)->foo('abc'));

        $testing = new Testing;
        $reg->register($testing, 'setVar', function ($var) {
            $this->var = $var;
        });
        $testing->setVar('bar');
        $this->assertSame('bar', $testing->var);
    }

    public function testRegisteredMacros()
    {
        $reg = new MacroRegistrar;
        $reg->registerMacros($this->app);

        $this->app['url']->setRootControllerNamespace('Foo');
        $this->assertSame('Foo', $this->app['url']->getRootControllerNamespace());

        $this->app['router']->middlewareGroup('foo', []);
        $this->assertTrue($this->app['router']->hasMiddlewareGroup('foo'));
    }
}

class Testing
{
    use Macroable;

    public $var;
}

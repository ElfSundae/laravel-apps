<?php

namespace ElfSundae\Laravel\Apps\Test;

use Illuminate\Support\Traits\Macroable;
use ElfSundae\Laravel\Apps\MacroRegistrar;

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
}

class Testing
{
    use Macroable;

    public $var;
}

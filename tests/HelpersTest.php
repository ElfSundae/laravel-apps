<?php

namespace ElfSundae\Laravel\Apps\Test;

use Mockery as m;

class HelpersTest extends TestCase
{
    public function test_app_id()
    {
        $apps = m::mock('stdClass');
        $apps->shouldReceive('id')
            ->once()
            ->with('foo')
            ->andReturn('bar');

        $this->app->instance('apps', $apps);
        $this->assertSame('bar', app_id('foo'));
    }
}

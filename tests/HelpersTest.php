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
            ->with('foo', 'bar')
            ->andReturn('result');
        $this->app->instance('apps', $apps);
        $this->assertSame('result', app_id('foo', 'bar'));
    }

    public function test_app_url()
    {
        $apps = m::mock('stdClass');
        $apps->shouldReceive('url')
            ->once()
            ->with('a', 'b', 'c')
            ->andReturn('result');
        $this->app->instance('apps', $apps);
        $this->assertSame('result', app_url('a', 'b', 'c'));
    }
}

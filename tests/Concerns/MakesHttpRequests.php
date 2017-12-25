<?php

namespace ElfSundae\Apps\Test\Concerns;

use Illuminate\Foundation\Testing\TestResponse;

trait MakesHttpRequests
{
    /**
     * Assert that the response is a superset of the given JSON.
     *
     * @param  mixed  $response
     * @param  array  $data
     * @return mixed
     */
    protected function assertJsonResponse($response, array $data)
    {
        if ($response instanceof TestResponse) {
            return $response->assertJson($data);
        }

        return $this->seeJsonSubset($data);
    }
}

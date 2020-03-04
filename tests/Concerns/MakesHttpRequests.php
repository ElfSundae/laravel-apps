<?php

namespace ElfSundae\Apps\Test\Concerns;

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
        if (method_exists($response, 'assertJson')) {
            return $response->assertJson($data);
        }

        return $this->seeJsonSubset($data);
    }
}

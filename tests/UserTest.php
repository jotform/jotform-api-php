<?php

namespace Tests;

class UserTest extends TestCase
{
    /** @test */
    public function test_get_user()
    {
        $response = $this->jotform->user()->get();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_user_usage()
    {
        $response = $this->jotform->user()->usage();
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_user_forms()
    {
        $response = $this->jotform->user()->forms();
        $this->assertEquals(200, $response->getStatusCode());
    }
}

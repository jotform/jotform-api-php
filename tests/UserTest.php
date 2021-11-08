<?php

namespace Tests;

class UserTest extends TestCase
{
    /** @test */
    public function test_get_user()
    {
        $this->jotform->user()->get();
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_user_usage()
    {
        $this->jotform->user()->usage();
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_user_forms()
    {
        $this->jotform->user()->forms();
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }
}

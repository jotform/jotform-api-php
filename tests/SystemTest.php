<?php

namespace Tests;

class SystemTest extends TestCase
{
    /** @test */
    public function test_get_system_plan_for_free()
    {
        $this->jotform->system()->plan('free');
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_bronze()
    {
        $this->jotform->system()->plan('bronze');
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_silver()
    {
        $this->jotform->system()->plan('silver');
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_gold()
    {
        $this->jotform->system()->plan('gold');
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_platinum()
    {
        $this->jotform->system()->plan('platinum');
        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }
}

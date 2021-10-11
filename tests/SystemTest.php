<?php

namespace Tests;

class SystemTest extends TestCase
{
    /** @test */
    public function test_get_system_plan_for_free()
    {
        $response = $this->jotform->system()->plan('free');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_bronze()
    {
        $response = $this->jotform->system()->plan('bronze');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_silver()
    {
        $response = $this->jotform->system()->plan('silver');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_gold()
    {
        $response = $this->jotform->system()->plan('gold');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /** @test */
    public function test_get_system_plan_for_platinum()
    {
        $response = $this->jotform->system()->plan('platinum');
        $this->assertEquals(200, $response->getStatusCode());
    }
}

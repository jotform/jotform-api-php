<?php

namespace Tests;

use Jotform\Exceptions\JotformException;

class ReportTest extends TestCase
{
    /** @test */
    public function test_get_report_with_invalid_id()
    {
        // [TODO]
        // $this->expectException(JotformException::class);
        $response = $this->jotform->report("<REPORT_ID>")->get();
        $report = $response->toArray();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($report);
    }
}

<?php

namespace Tests;

use Jotform\Exceptions\JotformException;

class ReportTest extends TestCase
{
    /** @test */
    public function test_get_report_with_invalid_id()
    {
        // [TODO]
        $this->expectException(JotformException::class);
        $report = $this->jotform->report("<REPORT_ID>")->get();

        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
        $this->assertEmpty($report);
    }
}

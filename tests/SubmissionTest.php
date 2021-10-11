<?php

namespace Tests;

use Jotform\Exceptions\JotformException;

class SubmissionTest extends TestCase
{
    /** @test */
    public function test_get_submission_with_invalid_id()
    {
        // [TODO]
        $this->expectException(JotformException::class);
        $this->jotform->submission("<SUBMISSON_ID>")->get();
    }

    /** @test */
    public function test_get_all_submissions_by_form_id()
    {
        // [TODO]
        $response = $this->jotform->form("<FORM_ID>")
            ->limit(100)->orderBy('created_at')->getSubmissions();

        $this->assertEquals(200, $response->getStatusCode());
    }
}

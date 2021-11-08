<?php

namespace Tests;

use Jotform\Exceptions\JotformException;

class SubmissionTest extends TestCase
{
    /** @test */
    // public function test_get_submission_with_invalid_id()
    // {
    //     $this->expectException(JotformException::class);
    //     $submissions = $this->jotform->form('111222333444')->submissions();
    //     $this->jotform->submission($submissions[0]['id'])->get();
    // }

    /** @test */
    public function test_get_submission_with_valid_id()
    {
        $forms = $this->jotform->user()->forms();
        $forms = array_filter($forms, function ($form) {
            return !is_null($form['last_submission']);
        });
        $form = array_shift($forms);
        $submissions = $this->jotform->form($form['id'])->submissions();
        $this->jotform->submission($submissions[0]['id'])->get();

        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }

    /** @test */
    public function test_get_all_submissions_by_form_id()
    {
        // [TODO]
        $forms = $this->jotform->user()->forms();
        $response = $this->jotform->form($forms[0]['id'])
            ->limit(100)->orderBy('created_at')->submissions();

        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
    }
}

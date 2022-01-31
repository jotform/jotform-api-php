<?php

namespace Tests;

use Jotform\Exceptions\JotformException;

class FormTest extends TestCase
{
    /** @test */
    public function test_get_form_with_invalid_id()
    {
        // [TODO]
        $this->expectException(JotformException::class);
        $this->jotform->form("<FORM_ID>")->get();
    }
}

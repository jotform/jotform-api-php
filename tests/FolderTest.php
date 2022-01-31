<?php

namespace Tests;

class FolderTest extends TestCase
{
    /** @test */
    public function test_get_folder_with_invalid_id()
    {
        // [TODO]
        $folder = $this->jotform->folder("<FOLDER_ID>")->get();

        $this->assertEquals(200, $this->jotform->response()->getStatusCode());
        $this->assertEmpty($folder);
    }
}

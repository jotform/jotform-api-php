<?php

namespace Tests;

class FolderTest extends TestCase
{
    /** @test */
    public function test_get_folder_with_invalid_id()
    {
        // [TODO]
        $response = $this->jotform->folder("<FOLDER_ID>")->get();
        $folder = $response->toObject();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($folder);
    }
}

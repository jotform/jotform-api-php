<?php

namespace Tests;

use Jotform\Jotform;
use Jotform\JotformClient;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** @var JotformClient */
    protected $client;

    /** @var Jotform */
    protected $jotform;

    protected function setUp(): void
    {
        // [TODO]
        $this->client = new JotformClient($_ENV['API_KEY']);
        $this->jotform = new Jotform($this->client);
    }
}

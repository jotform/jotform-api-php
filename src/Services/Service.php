<?php

namespace Jotform\Services;

use Jotform\JotformClient;

abstract class Service
{
    /** @var JotformClient */
    protected $client;

    /** @var string */
    protected $name;

    public function __construct(JotformClient $client)
    {
        $this->client = $client;
    }
}

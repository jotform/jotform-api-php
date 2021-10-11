<?php

namespace Jotform\Services;

use Jotform\JotformResponse;

class System extends Service
{
    /** @var string */
    protected $name = 'system';

    public function id(): string
    {
        return $this->folderId;
    }

    public function plan(string $name): JotformResponse
    {
        return $this->client->get("{$this->name}/plan/" . strtoupper($name));
    }
}

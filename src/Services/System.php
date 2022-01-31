<?php

namespace Jotform\Services;

class System extends Service
{
    /** @var string */
    protected $name = 'system';

    public function id(): string
    {
        return $this->folderId;
    }

    public function plan(string $name): ?array
    {
        return $this->client->get("{$this->name}/plan/" . strtoupper($name));
    }
}

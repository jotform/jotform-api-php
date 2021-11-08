<?php

namespace Jotform\Services;

use Jotform\JotformClient;
use Jotform\JotformResponse;

class Folder extends Service
{
    /** @var string */
    protected $name = 'folder';

    /** @var string */
    protected $folderId;

    public function __construct(JotformClient $client, string $folderId)
    {
        parent::__construct($client);
        $this->folderId = $folderId;
    }

    public function id(): string
    {
        return $this->folderId;
    }

    public function get(): ?array
    {
        return $this->client->get("{$this->name}/{$this->folderId}");
    }
}

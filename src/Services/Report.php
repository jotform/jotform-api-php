<?php

namespace Jotform\Services;

use Jotform\JotformClient;
use Jotform\JotformResponse;

class Report extends Service
{
    /** @var string */
    protected $name = 'report';

    /** @var string */
    protected $reportId;

    public function __construct(JotformClient $client, string $reportId)
    {
        parent::__construct($client);
        $this->reportId = $reportId;
    }

    public function id(): string
    {
        return $this->reportId;
    }

    public function get(): ?array
    {
        return $this->client->get("{$this->name}/{$this->reportId}");
    }

    public function delete(): ?array
    {
        return $this->client->delete("{$this->name}/{$this->reportId}");
    }
}

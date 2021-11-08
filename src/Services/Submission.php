<?php

namespace Jotform\Services;

use Jotform\JotformClient;
use Jotform\JotformResponse;

class Submission extends Service
{
    /** @var string */
    protected $name = 'submission';

    /** @var string */
    protected $submissionId;

    public function __construct(JotformClient $client, ?string $submissionId)
    {
        parent::__construct($client);
        $this->submissionId = $submissionId;
    }

    public function id(): string
    {
        return $this->submissionId;
    }

    public function get(): ?array
    {
        return $this->client->get("{$this->name}/{$this->submissionId}");
    }

    public function getAll(): ?array
    {
        return $this->client->get("{$this->name}/{$this->submissionId}");
    }

    /**
     * @param  string        $formId
     * @param  array|string  $params Data Array or JSON String
     * @return JotformResponse
     */
    public function create(string $formId, $params): ?array
    {
        $endpoint = "form/{$formId}/submissions";

        if (is_string($params)) {
            return $this->client->putJson($endpoint, $params);
        }

        return $this->client->post($endpoint, $this->prepareParams($params));
    }

    public function update(array $params): ?array
    {
        return $this->client->post("{$this->name}/{$this->submissionId}", $this->prepareParams($params));
    }

    public function delete(): ?array
    {
        return $this->client->delete("{$this->name}/{$this->submissionId}");
    }

    protected function prepareParams(array $params): array
    {
        $submission = [];
        foreach ($params as $key => $value) {
            if (strpos($key, '_') && $key !== 'created_at') {
                $qid = substr($key, 0, strpos($key, '_'));
                $type = substr($key, strpos($key, '_') + 1);
                $submission["submission[{$qid}][{$type}]"] = $value;
            } else {
                $submission["submission[{$key}]"] = $value;
            }
        }

        return $submission;
    }
}

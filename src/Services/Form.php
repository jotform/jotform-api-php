<?php

namespace Jotform\Services;

use Jotform\JotformClient;
use Jotform\JotformResponse;
use Jotform\Traits\UseConditions;

class Form extends Service
{
    use UseConditions;

    /** @var string */
    protected $name = 'form';

    /** @var string */
    protected $formId;

    public function __construct(JotformClient $client, ?string $formId)
    {
        parent::__construct($client);
        $this->formId = $formId;
    }

    public function id(): string
    {
        return $this->formId;
    }

    public function get(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}");
    }

    /**
     * @param  array|string  $params Array or Json
     * @return JotformResponse
     */
    public function create($params): ?array
    {
        if (is_string($params)) {
            return $this->client->putJson($this->name, $params);
        }

        $form = [];
        foreach ($params as $key => $value) {
            foreach ($value as $k => $v) {
                if ($key === "properties") {
                    $form["{$key}[{$k}]"] = $v;
                } else {
                    foreach ($v as $a => $b) {
                        $form["{$key}[{$k}][{$a}]"] = $b;
                    }
                }
            }
        }

        return $this->client->post($this->name, $form);
    }

    public function delete(): ?array
    {
        return $this->client->delete("{$this->name}/{$this->formId}");
    }

    public function clone(string $formId): ?array
    {
        return $this->client->post("{$this->name}/{$formId}/clone");
    }

    public function questions(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/questions", $this->getConditions());
    }

    public function question(string $questionId): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/questions/{$questionId}");
    }

    /**
     * @param  array|string  $params Data Array or JSON String
     * @return JotformResponse
     */
    public function createQuestion($params): ?array
    {
        $endpoint = "{$this->name}/{$this->formId}/questions";

        if (is_string($params)) {
            return $this->client->putJson($endpoint, $params);
        }

        return $this->client->post($endpoint, $this->prepareQuestionParams($params));
    }

    public function editQuestion(string $questionId, array $params): ?array
    {
        return $this->client->post(
            "{$this->name}/{$this->formId}/question/{$questionId}",
            $this->prepareQuestionParams($params)
        );
    }

    public function deleteQuestion(string $questionId): ?array
    {
        return $this->client->delete("{$this->name}/{$this->formId}/question/{$questionId}");
    }

    public function submissions(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/submissions", $this->getConditions());
    }

    public function createSubmission(array $params): ?array
    {
        return (new Submission($this->client, null))->create($this->formId, $params);
    }

    public function files(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/files", $this->getConditions());
    }

    public function webhooks(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/webhooks", $this->getConditions());
    }

    public function createWebhook(string $url): ?array
    {
        return $this->client->post("{$this->name}/{$this->formId}/webhooks", [
            'webhookURL' => $url,
        ]);
    }

    public function deleteWebhook(string $webhookId): ?array
    {
        return $this->client->delete("{$this->name}/{$this->formId}/webhooks/{$webhookId}");
    }

    public function properties(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/properties");
    }

    public function property(string $key): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/properties/{$key}");
    }

    /**
     * @param  array|string  $params Data Array or JSON String
     * @return JotformResponse
     */
    public function setProperties($params): ?array
    {
        $endpoint = "{$this->name}/{$this->formId}/properties";

        if (is_string($params)) {
            return $this->client->putJson($endpoint, $params);
        }

        $properties = [];
        foreach ($params as $key => $value) {
            $properties["properties[{$key}]"] = $value;
        }

        return $this->client->post($endpoint, $properties);
    }

    public function reports(): ?array
    {
        return $this->client->get("{$this->name}/{$this->formId}/reports", $this->getConditions());
    }

    public function createReport(array $params): ?array
    {
        return $this->client->post("{$this->name}/{$this->formId}/reports", $params);
    }

    protected function prepareQuestionParams(array $params): array
    {
        $question = [];
        foreach ($params as $key => $value) {
            $question["question[{$key}]"] = $value;
        }

        return $question;
    }
}

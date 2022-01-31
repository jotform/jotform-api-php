<?php

namespace Jotform;

use Jotform\Services\Folder;
use Jotform\Services\Form;
use Jotform\Services\Report;
use Jotform\Services\Submission;
use Jotform\Services\System;
use Jotform\Services\User;

class Jotform
{
    /** @var JotformClient */
    private $client;

    public function __construct(JotformClient $client, bool $euCheck = true)
    {
        $this->client = $client;

        // Check 'euOnly' flag for User while initializing.
        if ($euCheck) {
            $user = $this->user()->get();
            if (isset($user['euOnly'])) {
                $this->client->europeOnly(true);
            }
        }
    }

    public function europeOnly(bool $europeOnly): void
    {
        $this->client->europeOnly($europeOnly);
    }

    public function user(): User
    {
        return new User($this->client);
    }

    public function form(string $formId): Form
    {
        return new Form($this->client, $formId);
    }

    public function submission(string $submissionId): Submission
    {
        return new Submission($this->client, $submissionId);
    }

    public function report(string $reportId): Report
    {
        return new Report($this->client, $reportId);
    }

    public function folder(string $folderId): Folder
    {
        return new Folder($this->client, $folderId);
    }

    public function system(): System
    {
        return new System($this->client);
    }

    public function response(): JotformResponse
    {
        return $this->client->response;
    }
}

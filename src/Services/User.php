<?php

namespace Jotform\Services;

use Jotform\JotformResponse;
use Jotform\Traits\UseConditions;
use Jotform\Traits\UseQuery;

class User extends Service
{
    use UseConditions;
    use UseQuery;

    /** @var string */
    protected $name = 'user';

    public function get(): JotformResponse
    {
        return $this->client->get($this->name);
    }

    public function register(array $user): JotformResponse
    {
        return $this->client->post("{$this->name}/register", $user);
    }

    public function login(array $credentials): JotformResponse
    {
        return $this->client->post("{$this->name}/login", $credentials);
    }

    public function logout(): JotformResponse
    {
        return $this->client->get("{$this->name}/logout");
    }

    public function usage(): JotformResponse
    {
        return $this->client->get("{$this->name}/usage");
    }

    public function forms(): JotformResponse
    {
        return $this->client->get("{$this->name}/forms", $this->getConditions());
    }

    public function createForm(array $params): JotformResponse
    {
        return (new Form($this->client, null))->create($params);
    }

    public function submissions(): JotformResponse
    {
        return $this->client->get("{$this->name}/submissions", $this->getConditions());
    }

    public function subUsers(): JotformResponse
    {
        return $this->client->get("{$this->name}/subusers");
    }

    public function folders(): JotformResponse
    {
        return $this->client->get("{$this->name}/folders");
    }

    public function reports(): JotformResponse
    {
        return $this->client->get('{$this->name}/reports');
    }

    public function settings(): JotformResponse
    {
        return $this->client->get("{$this->name}/settings");
    }

    public function updateSettings(array $params): JotformResponse
    {
        return $this->client->post("{$this->name}/settings", $params);
    }

    public function history(): JotformResponse
    {
        return $this->client->get("{$this->name}/history", $this->getQueries());
    }
}

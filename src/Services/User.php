<?php

namespace Jotform\Services;

use Jotform\Traits\UseConditions;
use Jotform\Traits\UseQuery;

class User extends Service
{
    use UseConditions;
    use UseQuery;

    /** @var string */
    protected $name = 'user';

    public function get(): ?array
    {
        return $this->client->get($this->name);
    }

    public function register(array $user): ?array
    {
        return $this->client->post("{$this->name}/register", $user);
    }

    public function login(array $credentials): ?array
    {
        return $this->client->post("{$this->name}/login", $credentials);
    }

    public function logout(): ?array
    {
        return $this->client->get("{$this->name}/logout");
    }

    public function usage(): ?array
    {
        return $this->client->get("{$this->name}/usage");
    }

    public function forms(): ?array
    {
        return $this->client->get("{$this->name}/forms", $this->getConditions());
    }

    public function createForm(array $params): ?array
    {
        return (new Form($this->client, null))->create($params);
    }

    public function submissions(): ?array
    {
        return $this->client->get("{$this->name}/submissions", $this->getConditions());
    }

    public function subUsers(): ?array
    {
        return $this->client->get("{$this->name}/subusers");
    }

    public function folders(): ?array
    {
        return $this->client->get("{$this->name}/folders");
    }

    public function reports(): ?array
    {
        return $this->client->get("{$this->name}/reports");
    }

    public function settings(): ?array
    {
        return $this->client->get("{$this->name}/settings");
    }

    public function updateSettings(array $params): ?array
    {
        return $this->client->post("{$this->name}/settings", $params);
    }

    public function history(): ?array
    {
        return $this->client->get("{$this->name}/history", $this->getQueries());
    }
}

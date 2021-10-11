<?php

namespace Jotform\Traits;

trait UseQuery
{
    private $action;
    private $date;
    private $sortBy;
    private $startDate;
    private $endDate;

    public function action(int $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function date(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function sortBy(array $sortBy): self
    {
        $this->sortBy = urlencode(json_encode($sortBy));

        return $this;
    }

    public function startDate(string $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function endDate(string $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    protected function getQueries()
    {
        $queries = [];

        return $queries;
    }
}

<?php

namespace Jotform\Traits;

trait UseQuery
{
    /** @var string */
    private $action;

    /** @var string */
    private $date;

    /** @var string */
    private $sortBy;

    /** @var string */
    private $startDate;

    /** @var string */
    private $endDate;

    public function action(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function date(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function sortBy(string $sortBy): self
    {
        $this->sortBy = $sortBy;

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

        if ($this->action) {
            $conditions['action'] = $this->action;
        }

        if ($this->date) {
            $conditions['date'] = $this->date;
        }

        if ($this->sortBy) {
            $conditions['sortBy'] = $this->sortBy;
        }

        if ($this->startDate) {
            $conditions['startDate'] = $this->startDate;
        }

        if ($this->endDate) {
            $conditions['endDate'] = $this->endDate;
        }

        return $queries;
    }
}

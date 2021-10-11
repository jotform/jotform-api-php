<?php

namespace Jotform\Traits;

trait UseConditions
{
    private $offset;
    private $limit;
    private $filter;
    private $orderBy;

    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function filter(array $filter): self
    {
        $this->filter = urlencode(json_encode($filter));

        return $this;
    }

    public function orderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    protected function getConditions()
    {
        $conditions = [];

        return $conditions;
    }
}

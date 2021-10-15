<?php

namespace Jotform\Traits;

trait UseConditions
{
    /** @var int */
    private $offset;

    /** @var int */
    private $limit;

    /** @var array */
    private $filter;

    /** @var string */
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
        $this->filter = $filter;

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

        if ($this->offset) {
            $conditions['offset'] = $this->offset;
        }

        if ($this->limit) {
            $conditions['limit'] = $this->limit;
        }

        if ($this->filter) {
            $conditions['filter'] = urlencode(json_encode($this->filter));
        }

        if ($this->orderBy) {
            $conditions['orderby'] = $this->orderBy;
        }

        return $conditions;
    }
}

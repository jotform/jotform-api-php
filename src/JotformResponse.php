<?php

namespace Jotform;

class JotformResponse
{
    private $content;
    private $statusCode;
    private $message;

    public function __construct($content, int $statusCode, ?string $message)
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

    public function toJson(): ?string
    {
        return json_encode($this->content);
    }

    public function toObject(): ?object
    {
        $object = json_decode($this->toJson());

        return empty($object) ? null : $object;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function __toString(): string
    {
        return $this->toJson();
    }
}

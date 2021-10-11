<?php

namespace Jotform\Exceptions;

class ServiceUnavailableException extends JotformException
{
    public function __construct()
    {
        parent::__construct("Service is unavailable, rate limits etc exceeded!", 503);
    }
}

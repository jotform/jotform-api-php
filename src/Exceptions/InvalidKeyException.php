<?php

namespace Jotform\Exceptions;

class InvalidKeyException extends JotformException
{
    public function __construct()
    {
        parent::__construct("Invalid API key or Unauthorized API call", 401);
    }
}

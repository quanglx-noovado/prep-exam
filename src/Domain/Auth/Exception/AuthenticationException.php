<?php

namespace Src\Domain\Auth\Exception;

class AuthenticationException extends \Exception
{
    public function __construct(string $message = "Authentication failed")
    {
        parent::__construct($message, 401);
    }
}

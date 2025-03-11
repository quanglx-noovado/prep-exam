<?php

namespace Src\Domain\Auth\Exception;

class AuthenticationException extends \Exception
{
    public function __construct(string $message = "Authentication failed", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}

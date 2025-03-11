<?php

namespace Src\Domain\Auth\Exception;

class UserNotFoundException extends \Exception
{
    public function __construct(string $message = "User not found", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}

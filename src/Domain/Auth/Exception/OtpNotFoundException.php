<?php

namespace Src\Domain\Auth\Exception;

class OtpNotFoundException extends \Exception
{
    public function __construct(string $message = "Otp not found", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}

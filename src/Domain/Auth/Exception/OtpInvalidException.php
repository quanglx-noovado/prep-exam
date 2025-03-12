<?php

namespace Src\Domain\Auth\Exception;

class OtpInvalidException extends \Exception
{
    public function __construct(string $message = "Otp invalid exception", int $code = 422)
    {
        parent::__construct($message, $code);
    }
}

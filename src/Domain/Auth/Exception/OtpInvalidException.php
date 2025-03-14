<?php

namespace Src\Domain\Auth\Exception;

class OtpInvalidException extends \Exception
{
    public function __construct(string $message = "Otp không hợp lệ")
    {
        parent::__construct($message, 422);
    }
}

<?php

namespace Src\Domain\Auth\Exception;

class OtpNotFoundException extends \Exception
{
    public function __construct(string $message = "Không tìm thấy OTP")
    {
        parent::__construct($message, 404);
    }
}

<?php

namespace Src\Domain\Auth\Exception;

class OtpInvalidException extends \Exception
{
    public function __construct(string $message = "Mã otp không hợp lệ vui lòng nhập lại.")
    {
        parent::__construct($message, 422);
    }
}

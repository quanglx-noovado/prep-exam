<?php

namespace Src\Domain\Auth\Exception;

class VerifyOtpException extends \Exception
{
    public function __construct(
        string $message,
        int $code,
    ) {
        parent::__construct($message, $code);
    }
}

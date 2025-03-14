<?php

namespace Src\Domain\Auth\Exception;

class VerifyOtpException extends \Exception
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message, 429);
    }
}

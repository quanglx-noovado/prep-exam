<?php

namespace Src\Domain\Auth\Exception;

class SendOtpException extends \Exception
{
    public function __construct(
        string $message,
        int $code,
    ) {
        parent::__construct($message, $code);
    }
}

<?php

namespace Src\Domain\Auth\Exception;

class SendOtpException extends \Exception
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message, 429);
    }
}

<?php

namespace Src\Domain\Auth\Exception;

class DeviceInvalidException extends \Exception
{
    public function __construct(
        string $message,
        int $code,
        private readonly string $deviceToken,
    ) {
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }
}

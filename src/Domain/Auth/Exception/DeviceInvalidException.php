<?php

namespace Src\Domain\Auth\Exception;

class DeviceInvalidException extends \Exception
{
    public function __construct(
        string $message,
        private readonly string $deviceToken,
    ) {
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }
}

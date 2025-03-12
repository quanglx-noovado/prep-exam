<?php

namespace Src\Domain\Auth\Exception;

class DeviceLimitExceededException extends \Exception
{
    public function __construct(
        string $message,
        private readonly array $activeDevices,
        private readonly string $deviceToken,
    ) {
        parent::__construct($message);
    }

    public function getActiveDevices(): array
    {
        return $this->activeDevices;
    }

    /**
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }
}

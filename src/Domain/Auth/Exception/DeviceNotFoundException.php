<?php

namespace Src\Domain\Auth\Exception;

class DeviceNotFoundException extends \Exception
{
    public function __construct(string $message = "Device not found", int $code = 404)
    {
        parent::__construct($message, $code);
    }
}

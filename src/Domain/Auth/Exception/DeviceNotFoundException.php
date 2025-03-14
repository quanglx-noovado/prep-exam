<?php

namespace Src\Domain\Auth\Exception;

class DeviceNotFoundException extends \Exception
{
    public function __construct(string $message = "Không tìm thấy thiết bị")
    {
        parent::__construct($message, 404);
    }
}

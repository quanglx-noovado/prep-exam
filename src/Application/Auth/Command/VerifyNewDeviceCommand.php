<?php

namespace Src\Application\Auth\Command;

class VerifyNewDeviceCommand
{
    public function __construct(
        public readonly string $deviceToken,
        public readonly string $otpCode,
    ) {
    }
}

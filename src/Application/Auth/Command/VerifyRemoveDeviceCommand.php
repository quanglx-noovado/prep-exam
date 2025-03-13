<?php

namespace Src\Application\Auth\Command;

class VerifyRemoveDeviceCommand
{
    public function __construct(
        public readonly string $deviceToken,
        public readonly string $otpCode,
        public readonly array $removeDeviceTokens,
    ) {
    }
}

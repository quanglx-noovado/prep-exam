<?php

namespace Src\Application\Auth\Command;

use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;

class SendOtpCommand
{
    public function __construct(
        public readonly string $deviceToken,
        public readonly OtpSendType $type,
        public readonly OtpPurpose $purpose,
    ) {
    }
}

<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Enum\OtpSendType;

class SendOTPFactory
{
    public static function create(OtpSendType $type): SendOTPServiceAbstract
    {
        return match ($type) {
            OtpSendType::SMS => app(SendOTPBySms::class),
            OtpSendType::EMAIL => app(SendOTPByEmail::class),
        };
    }
}


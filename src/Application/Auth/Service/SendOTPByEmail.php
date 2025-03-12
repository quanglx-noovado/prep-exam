<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Otp;

class SendOTPByEmail extends SendOTPServiceAbstract
{
    public function sendOTP(Otp $otp): void
    {
        // Send OTP by email
    }
}

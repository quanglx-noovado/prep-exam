<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Otp;

class SendOTPBySms extends SendOTPServiceAbstract
{
    public function sendOTP(Otp $otp): void
    {
        // Send OTP by email
    }
}

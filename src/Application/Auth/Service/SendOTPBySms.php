<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Entity\User;

class SendOTPBySms extends SendOTPServiceAbstract
{
    public function sendOTP(Otp $otp, User $user): void
    {
        // Send OTP by email
    }
}

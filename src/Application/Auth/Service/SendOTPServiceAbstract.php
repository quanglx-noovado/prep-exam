<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Otp;

abstract class SendOTPServiceAbstract
{
    abstract public function sendOTP(Otp $otp): void;
}

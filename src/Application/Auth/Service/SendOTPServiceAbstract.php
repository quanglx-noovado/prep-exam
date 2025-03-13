<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Entity\User;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Repository\OtpRepository;

abstract class SendOTPServiceAbstract
{

    public function __construct(
        private readonly OtpRepository $otpRepository
    ) {
    }

    abstract public function sendOTP(Otp $otp, User $user): void;


    public function updateSentOTP(Otp $otp): void
    {
        $otp->updateStatus(OtpStatus::SENT);
        $this->otpRepository->update($otp);
    }
}

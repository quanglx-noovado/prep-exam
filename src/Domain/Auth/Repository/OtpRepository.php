<?php

namespace Src\Domain\Auth\Repository;

use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Enum\OtpPurpose;

interface OtpRepository
{
    public function getActiveOtp(int $userId, int $deviceId, OtpPurpose $purpose): Otp;

    public function create(Otp $otp): Otp;

    public function update(Otp $otp): void;
}

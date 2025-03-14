<?php

namespace Src\Application\Auth\Service;

use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\OtpRepository;
use Src\Domain\Auth\Repository\UserRepository;

class OtpService
{
    public function __construct(
        private readonly OtpRepository $otpRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws OtpInvalidException
     * @throws UserNotFoundException
     */
    public function verify(Device $device, OtpPurpose $purpose, string $otpCode): void
    {
        $user = $this->userRepository->getById($device->getUserId());
        $otp = $this->otpRepository->getLatestOtp(
            userId: $user->getId(),
            deviceId: $device->getId(),
            purpose: $purpose
        );
        if ($otp->getExpiresAt()->isPast()) {
            throw new OtpInvalidException('OTP has expired');
        }

        if ($otp->getOtp() !== $otpCode) {
            throw new OtpInvalidException();
        }
        $otp->updateStatus(OtpStatus::CONFIRMED);
        $this->otpRepository->update($otp);
    }
}


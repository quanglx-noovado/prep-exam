<?php

namespace Src\Application\Auth\Command;

use App\Models\Device;
use Carbon\Carbon;
use Src\Domain\Auth\AuthService;
use Src\Domain\Auth\Entity\Device as DeviceEntity;
use Src\Domain\Auth\Entity\User;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Exception\DeviceLimitExceededException;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\DeviceRepository;
use Src\Domain\Auth\Repository\OtpRepository;
use Src\Domain\Auth\Repository\UserRepository;

class VerifyRemoveDeviceHandler
{
    public function __construct(
        private readonly OtpRepository $otpRepository,
        private readonly DeviceRepository $deviceRepository,
        private readonly UserRepository $userRepository,
        private readonly AuthService $authService,
    ) {
    }


    /**
     * Thiếu:
     * Logic check số lần gửi otp trong 1h. Có đang bị block không cho gửi otp khi nhập sai quá nhiều lần không
     * Check verify is null
     */

    /**
     *
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     * @throws OtpInvalidException
     * @throws DeviceLimitExceededException
     */
    public function handle(VerifyRemoveDeviceCommand $command): string
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $user = $this->userRepository->getById($device->getUserId());
        $otp = $this->otpRepository->getLatestOtp(
            userId: $user->getId(),
            deviceId: $device->getId(),
            purpose: OtpPurpose::REMOVE_DEVICE
        );
        if ($otp->getExpiresAt()->isPast()) {
            throw new OtpInvalidException('OTP has expired');
        }

        if ($otp->getOtp() !== $command->otpCode) {
            throw new OtpInvalidException();
        }
        foreach ($command->removeDeviceTokens as $removeDeviceToken) {
            $removeDevice = $this->deviceRepository->getByDeviceToken($removeDeviceToken);
            $removeDevice->updateIsActive(false);
            $this->deviceRepository->update($removeDevice);
        }

        $otp->updateStatus(OtpStatus::CONFIRMED);
        $this->otpRepository->update($otp);
        return $this->handleLogin($user, $device);
    }

    /**
     * @throws DeviceLimitExceededException
     */
    private function handleLogin(User $user, DeviceEntity $device): string
    {
        $countDeviceActive = $this->deviceRepository->countActiveDevice($user->getId());

        if ($countDeviceActive >= Device::MAX_ACTIVE_DEVICES) {
            throw new DeviceLimitExceededException(
                'Maximum active devices reached. Please deactivate another device first.',
                $device->getDeviceToken()
            );
        }
        $device->updateIsActive(true);
        $device->updateLastLoginAt(Carbon::now());
        $this->deviceRepository->update($device);

        return $this->authService->generateToken($user);
    }
}

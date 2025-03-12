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

class VerifyNewDeviceHandler
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
     * Logic báo otp đã expired
     * Ghi log khi otp không hợp lệ
     * Đếm số lần otp không hợp lệ để block user
     * Đang lặp hàm với bên login handler để check số device active
     * /
     *
     * /**
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     * @throws OtpInvalidException
     * @throws DeviceLimitExceededException
     */
    public function handle(VerifyNewDeviceCommand $command): string
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $user = $this->userRepository->getById($device->getUserId());
        $otp = $this->otpRepository->getActiveOtp(
            userId: $user->getId(),
            deviceId: $device->getId(),
            purpose: OtpPurpose::NEW_DEVICE
        );
        if ($otp->getOtp() !== $command->otpCode) {
            throw new OtpInvalidException();
        }
        $device->updateVerifiedAt(Carbon::now());
        $this->deviceRepository->update($device);
        $otp->updateStatus(OtpStatus::CONFIRMED);
        $this->otpRepository->update($otp);
        return $this->handleLogin($user, $device);
    }

    /**
     * @throws DeviceLimitExceededException
     */
    private function handleLogin(User $user, DeviceEntity $device): string
    {
        $activeDevices = $this->deviceRepository->getListActiveDevice($user->getId());

        if (count($activeDevices) >= Device::MAX_ACTIVE_DEVICES) {
            throw new DeviceLimitExceededException(
                'Maximum active devices reached. Please deactivate another device first.',
                $activeDevices,
                $device->getDeviceToken()
            );
        }
        $device->updateIsActive(true);
        $device->updateLastLoginAt(Carbon::now());
        $this->deviceRepository->update($device);

        return $this->authService->generateToken($user);
    }
}

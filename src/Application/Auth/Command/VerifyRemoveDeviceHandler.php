<?php

namespace Src\Application\Auth\Command;

use Src\Application\Auth\Service\OtpService;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\DeviceRepository;

class VerifyRemoveDeviceHandler
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly DeviceRepository $deviceRepository,
    ) {
    }


    /**
     * Thiếu:
     * Logic check số lần gửi otp trong 1h. Có đang bị block không cho gửi otp khi nhập sai quá nhiều lần không
     */

    /**
     *
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     * @throws OtpInvalidException
     */
    public function handle(VerifyRemoveDeviceCommand $command): void
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $this->otpService->verify($device, OtpPurpose::REMOVE_DEVICE, $command->otpCode);

        foreach ($command->removeDeviceTokens as $removeDeviceToken) {
            $removeDevice = $this->deviceRepository->getByDeviceToken($removeDeviceToken);
            $removeDevice->updateIsActive(false);
            $this->deviceRepository->update($removeDevice);
        }
    }
}

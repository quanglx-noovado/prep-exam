<?php

namespace Src\Application\Auth\Command;

use Src\Application\Auth\Service\OtpService;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Exception\VerifyOtpException;
use Src\Domain\Auth\Repository\DeviceRepository;

class VerifyRemoveDeviceHandler
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly DeviceRepository $deviceRepository,
    ) {
    }

    /**
     *
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     * @throws OtpInvalidException
     * @throws VerifyOtpException
     */
    public function handle(VerifyRemoveDeviceCommand $command): void
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $this->otpService->verifyOtpFailedTime($device->getUserId());
        $this->otpService->verify($device, OtpPurpose::REMOVE_DEVICE, $command->otpCode);

        foreach ($command->removeDeviceTokens as $removeDeviceToken) {
            $removeDevice = $this->deviceRepository->getByDeviceToken($removeDeviceToken);
            $removeDevice->updateIsActive(false);
            $this->deviceRepository->update($removeDevice);
        }
    }
}

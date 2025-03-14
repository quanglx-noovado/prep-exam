<?php

namespace Src\Application\Auth\Command;

use Carbon\Carbon;
use Src\Application\Auth\Service\OtpService;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Exception\VerifyOtpException;
use Src\Domain\Auth\Repository\DeviceRepository;

class VerifyNewDeviceHandler
{
    public function __construct(
        private readonly DeviceRepository $deviceRepository,
        private readonly OtpService $otpService,
    ) {
    }

    /**
     *
     * /**
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     * @throws OtpInvalidException
     * @throws VerifyOtpException
     */
    public function handle(VerifyNewDeviceCommand $command): void
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $this->otpService->verifyOtpFailedTime($device->getUserId());
        $this->otpService->verify($device, OtpPurpose::NEW_DEVICE, $command->otpCode);

        $device->updateVerifiedAt(Carbon::now());
        $this->deviceRepository->update($device);
    }
}

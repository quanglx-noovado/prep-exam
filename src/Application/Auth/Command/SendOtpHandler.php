<?php

namespace Src\Application\Auth\Command;

use App\Helpers\StringHelper;
use Carbon\Carbon;
use Src\Application\Auth\Service\SendOTPFactory;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\DeviceRepository;
use Src\Domain\Auth\Repository\OtpRepository;
use Src\Domain\Auth\Repository\UserRepository;

class SendOtpHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly DeviceRepository $deviceRepository,
        private readonly OtpRepository $otpRepository,
    ) {
    }

    /**
     * @throws DeviceNotFoundException
     * @throws UserNotFoundException
     */
    public function handle(SendOtpCommand $command): void
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $user = $this->userRepository->getById($device->getUserId());
        $otp = StringHelper::generateOtp();
        $entity = new Otp(
            userId: $user->getId(),
            deviceId: $device->getId(),
            otp: $otp,
            sentType: $command->type,
            status: OtpStatus::PENDING,
            purpose: $command->purpose,
            expiresAt: Carbon::now()->addMinutes(5)
        );
        $entity = $this->otpRepository->create($entity);
        $service = SendOTPFactory::create($command->type);
        $service->sendOTP($entity, $user);
    }
}

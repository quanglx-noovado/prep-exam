<?php

namespace Src\Application\Auth\Command;

use App\Helpers\StringHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Src\Application\Auth\Service\SendOTPFactory;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Entity\User;
use Src\Domain\Auth\Enum\OtpSendType;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Enum\RedisKey;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\SendOtpException;
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
     * @throws SendOtpException
     */
    public function handle(SendOtpCommand $command): void
    {
        $device = $this->deviceRepository->getByDeviceToken($command->deviceToken);
        $user = $this->userRepository->getById($device->getUserId());
        $this->verifySentOtpTime($user->getId());
        $otp = $this->createOtp($user, $device, $command);
        $this->sendOtp($otp, $user, $command->type);
    }

    /**
     * @throws SendOtpException
     */
    private function verifySentOtpTime(int $userId): void
    {
        $sentCountKey = RedisKey::SENT_COUNT->value . $userId;
        $lastSentKey = RedisKey::LAST_SENT->value . $userId;
        $sentCount = Redis::get($sentCountKey) ?? 0;
        $lastSent = Redis::get($lastSentKey);
        $lastSent = $lastSent === null ? null : Carbon::parse($lastSent);

        if ($sentCount >= 5 && !empty($lastSent) && $lastSent->addHour()->gt(Carbon::now())) {
            throw new SendOtpException('Bạn đã yêu cầu OTP quá nhiều lần. Vui lòng thử lại sau 1 giờ.', 429);
        }
        Redis::set($lastSentKey, Carbon::now()->format('Y-m-d H:i:s'));

        if ($sentCount < 5) {
            Redis::set($sentCountKey, $sentCount + 1);
        } else {
            Redis::set($sentCountKey, 1);
        }
    }

    private function createOtp(User $user, Device $device, SendOtpCommand $command): Otp
    {
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
        return $this->otpRepository->create($entity);
    }

    private function sendOtp(Otp $otp, User $user, OtpSendType $type): void
    {
        $service = SendOTPFactory::create($type);
        $service->sendOTP($otp, $user);
    }
}

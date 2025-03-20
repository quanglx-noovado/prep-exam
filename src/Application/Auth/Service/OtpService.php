<?php

namespace Src\Application\Auth\Service;

use App\Helpers\StringHelper;
use App\Jobs\WriteThroughOtpJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpStatus;
use Src\Domain\Auth\Enum\RedisKey;
use Src\Domain\Auth\Exception\OtpInvalidException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Exception\VerifyOtpException;
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
            $this->updateFailedCount($device->getUserId());
            throw new OtpInvalidException('OTP đã hết hạn, vui lòng gửi lại OTP khác');
        }

        if ($otp->getOtp() !== $otpCode) {
            $this->updateFailedCount($device->getUserId());
            throw new OtpInvalidException();
        }
        $this->updateFailedCount($device->getUserId(), true);

        $this->updateOtp($otp);
    }

    private function updateOtp(Otp $otp): void
    {
        $otp->updateStatus(OtpStatus::CONFIRMED);
        $key = StringHelper::genCacheKeyOtp($otp);
        Cache::put($key, $otp->toArray(), 60);

        WriteThroughOtpJob::dispatch($otp);
    }

    /**
     * @throws VerifyOtpException
     */
    public function verifyOtpFailedTime(int $userId): void
    {
        $failedCountKey = RedisKey::FAILED_COUNT->value . $userId;
        $lastFailedKey = RedisKey::LAST_FAILED->value . $userId;

        $failedCount = Cache::get($failedCountKey) ?? 0;
        $lastFailed = Cache::get($lastFailedKey);
        $lastFailed = $lastFailed === null ? null : Carbon::parse($lastFailed);

        if ($failedCount >= 5 && $lastFailed->addMinutes(30)->gt(Carbon::now())) {
            throw new VerifyOtpException('Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 30');
        }
    }

    private function updateFailedCount(int $userId, bool $isVerify = false): void
    {
        $failedCountKey = RedisKey::FAILED_COUNT->value . $userId;
        $lastFailedKey = RedisKey::LAST_FAILED->value . $userId;
        if ($isVerify) {
            Cache::set($failedCountKey, 0);
        } else {
            Cache::set($failedCountKey, Cache::get($failedCountKey) + 1);
            Cache::set($lastFailedKey, Carbon::now()->format('Y-m-d H:i:s'));
        }
    }
}


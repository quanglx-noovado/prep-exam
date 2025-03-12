<?php

namespace Src\Domain\Auth\Entity;

use Carbon\Carbon;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;
use Src\Domain\Auth\Enum\OtpStatus;

class Otp
{
    private ?int $id;

    public function __construct(
        private readonly int $userId,
        private readonly int $deviceId,
        private readonly string $otp,
        private readonly OtpSendType $sentType,
        private OtpStatus $status,
        private readonly OtpPurpose $purpose,
        private readonly Carbon $expiresAt,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    public function getOtp(): string
    {
        return $this->otp;
    }

    public function getSentType(): OtpSendType
    {
        return $this->sentType;
    }

    public function getStatus(): OtpStatus
    {
        return $this->status;
    }

    public function getPurpose(): OtpPurpose
    {
        return $this->purpose;
    }

    public function getExpiresAt(): Carbon
    {
        return $this->expiresAt;
    }

    public function updateStatus(OtpStatus $status): void
    {
        $this->status = $status;
    }
}

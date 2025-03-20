<?php

namespace Src\Domain\Auth\Entity;

use Carbon\Carbon;

class Device
{
    private ?int $id;

    public function __construct(
        private readonly int $userId,
        private readonly string $name,
        private readonly string $fingerPrint,
        private string $deviceToken,
        private bool $isActive,
        private ?Carbon $lastLoginAt,
        private ?Carbon $verifiedAt = null,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getFingerPrint(): string
    {
        return $this->fingerPrint;
    }

    public function getLastLoginAt(): ?Carbon
    {
        return $this->lastLoginAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }

    /**
     * @return Carbon|null
     */
    public function getVerifiedAt(): ?Carbon
    {
        return $this->verifiedAt;
    }

    public function updateDeviceToken(string $deviceToken): void
    {
        $this->deviceToken = $deviceToken;
    }

    public function updateLastLoginAt(?Carbon $lastLoginAt): void
    {
        $this->lastLoginAt = $lastLoginAt;
    }

    public function updateIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function updateVerifiedAt(?Carbon $verifiedAt): void
    {
        $this->verifiedAt = $verifiedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'name' => $this->name,
            'finger_print' => $this->fingerPrint,
            'device_token' => $this->deviceToken,
            'is_active' => $this->isActive,
            'last_login_at' => $this->lastLoginAt?->format('Y-m-d H:i:s'),
            'verified_at' => $this->verifiedAt?->format('Y-m-d H:i:s'),
        ];
    }
}

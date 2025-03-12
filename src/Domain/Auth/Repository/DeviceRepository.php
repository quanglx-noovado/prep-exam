<?php

namespace Src\Domain\Auth\Repository;

use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Exception\DeviceNotFoundException;

interface DeviceRepository
{
    /**
     * @throws DeviceNotFoundException
     */
    public function getByUserAndFingerPrint(int $userId, string $fingerPrint): Device;

    /**
     * @throws DeviceNotFoundException
     */
    public function getByDeviceToken(string $deviceToken): Device;

    public function update(Device $device): void;

    public function create(Device $device): Device;

    public function getListActiveDevice(int $userId): array;
}

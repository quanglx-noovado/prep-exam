<?php

namespace Src\Infrastructure\Persistence\Mysql;

use App\Models\Device as DeviceModel;
use Carbon\Carbon;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Repository\DeviceRepository;

class MysqlDeviceRepository implements DeviceRepository
{
    /**
     * @throws DeviceNotFoundException
     */
    public function getByUserAndFingerPrint(int $userId, string $fingerPrint): Device
    {
        $device = DeviceModel::query()->where('user_id', $userId)->where('finger_print', $fingerPrint)->first();

        if (!$device) {
            throw new DeviceNotFoundException();
        }

        return $this->buildEntity($device);
    }

    /**
     * @throws DeviceNotFoundException
     */
    public function getByDeviceToken(string $deviceToken): Device
    {
        $device = DeviceModel::query()->where('device_token', $deviceToken)->first();

        if (!$device) {
            throw new DeviceNotFoundException();
        }

        return $this->buildEntity($device);
    }

    public function update(Device $device): void
    {
        DeviceModel::query()
            ->where('id', $device->getId())
            ->update([
                'last_login_at' => $device->getLastLoginAt(),
                'is_active' => $device->isActive(),
                'device_token' => $device->getDeviceToken(),
                'verified_at' => $device->getVerifiedAt(),
            ]);
    }

    public function create(Device $device): Device
    {
        $model = DeviceModel::query()
            ->create([
                'user_id' => $device->getUserId(),
                'name' => $device->getName(),
                'finger_print' => $device->getFingerPrint(),
                'device_token' => $device->getDeviceToken(),
                'is_active' => $device->isActive(),
                'last_login_at' => $device->getLastLoginAt(),
                'verified_at' => $device->getVerifiedAt(),
            ]);

        $device->setId($model->id);

        return $device;
    }

    /**
     * @param int $userId
     * @return Device[]
     */
    public function getListActiveDevice(int $userId): array
    {
        $devices = DeviceModel::query()->where('user_id', $userId)->where('is_active', true)->get();
        $data = [];
        foreach ($devices as $device) {
            $data[] = $this->buildEntity($device);
        }

        return $data;
    }

    private function buildEntity(DeviceModel $device): Device
    {
        $entity = new Device(
            userId: $device->user_id,
            name: $device->name,
            fingerPrint: $device->finger_print,
            deviceToken: $device->device_token,
            isActive: $device->is_active,
            lastLoginAt: $device->last_login_at === null ? null : Carbon::parse($device->last_login_at),
            verifiedAt: $device->verified_at === null ? null : Carbon::parse($device->verified_at),
        );

        $entity->setId($device->id);

        return $entity;
    }
}

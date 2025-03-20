<?php

namespace Src\Application\Auth\Service;

use App\Helpers\StringHelper;
use App\Jobs\WriteThroughDeviceJob;
use Illuminate\Support\Facades\Cache;
use Src\Domain\Auth\Entity\Device as DeviceEntity;

class DeviceService
{
    public function __construct()
    {
    }

    public function updateDevice(DeviceEntity $device): void
    {
        Cache::put(StringHelper::genCacheKeyDeviceToken($device->getDeviceToken()), $device->toArray(), 60);
        $keyDeviceActives = StringHelper::genCacheKeyDeviceActive($device->getUserId());
        $deviceActiveIds = Cache::get($keyDeviceActives, []);
        if ($device->isActive()) {
            if (!in_array($device->getId(), $deviceActiveIds)) {
                $deviceActiveIds[] = $device->getId();
            }
        } else {
            $deviceActiveIds = array_filter($deviceActiveIds, fn($id) => $id !== $device->getId());
        }
        Cache::put($keyDeviceActives, $deviceActiveIds);

        WriteThroughDeviceJob::dispatch($device);
    }
}


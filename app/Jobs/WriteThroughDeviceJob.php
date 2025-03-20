<?php

namespace App\Jobs;

use App\Helpers\StringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Repository\DeviceRepository;

class WriteThroughDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Device $device,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(DeviceRepository $deviceRepository): void
    {
        $deviceRepository->update($this->device);
    }

    public function fail($exception = null): void
    {
        Cache::forget(StringHelper::genCacheKeyDeviceToken($this->device->getDeviceToken()));
        Cache::forget(StringHelper::genCacheKeyDeviceActive($this->device->getUserId()));
    }
}

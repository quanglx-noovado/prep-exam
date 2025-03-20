<?php

namespace App\Jobs;

use App\Helpers\StringHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Repository\OtpRepository;

class WriteThroughOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly Otp $otp,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(OtpRepository $otpRepository): void
    {
        $otpRepository->update($this->otp);
    }

    public function fail($exception = null): void
    {
        Cache::forget(StringHelper::genCacheKeyOtp($this->otp));
    }
}

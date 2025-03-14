<?php

namespace App\Listeners;

use App\Events\OtpCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Src\Application\Auth\Service\OtpService;
use Src\Application\Auth\Service\SendOTPFactory;

class SendOtpListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly OtpService $otpService
    ) {
    }

    public function handle(OtpCreatedEvent $event): void
    {
        $service = SendOTPFactory::create($event->otp->getSentType());
        $service->sendOTP($event->otp, $event->user);
    }
}

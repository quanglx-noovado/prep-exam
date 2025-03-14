<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Entity\User;

class OtpCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Otp $otp,
        public readonly User $user,
    ) {
    }
}

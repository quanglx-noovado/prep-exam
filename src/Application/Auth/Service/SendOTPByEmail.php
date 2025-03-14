<?php

namespace Src\Application\Auth\Service;

use Illuminate\Support\Facades\Mail;
use Src\Domain\Auth\Entity\Otp;
use Src\Domain\Auth\Entity\User;

class SendOTPByEmail extends SendOTPServiceAbstract
{
    public function sendOTP(Otp $otp, User $user): void
    {
        Mail::raw('Otp của bạn là: ' . $otp->getOtp(), function ($message) use ($user) {
            $message->to($user->getEmail())
                ->subject('Gửi mã OTP');
        });

        $this->updateStatusOTP($otp);
    }
}

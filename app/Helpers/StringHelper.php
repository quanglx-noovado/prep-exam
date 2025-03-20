<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Src\Domain\Auth\Entity\Otp;

class StringHelper
{
    public static function createDeviceToken(int $userId, string $fingerPrint): string
    {
        $deviceInfo = $userId . '|' . $fingerPrint;

        return Hash::make($deviceInfo);
    }

    /**
     * Generate OTP
     *
     * @return string
     */
    public static function generateOtp(): string
    {
        return str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function genCacheKeyDeviceToken(string $deviceToken): string
    {
        return 'device_token_' . $deviceToken;
    }

    public static function genCacheKeyOtp(Otp $otp): string
    {
        return 'otp_user_id_' . $otp->getUserId() . '_device_id_' . $otp->getDeviceId() . '_purpose_' . $otp->getPurpose()->value;
    }

    public static function genCacheKeyDeviceActive(int $userId): string
    {
        return 'device_actives_user_id_' . $userId;
    }

    public static function generateFingerPrint(array $data): string
    {
        return hash('sha256', json_encode($data));
    }
}

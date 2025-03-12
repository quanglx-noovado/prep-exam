<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;

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
}

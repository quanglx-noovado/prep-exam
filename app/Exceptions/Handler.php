<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Src\Domain\Auth\Exception\DeviceInvalidException;
use Src\Domain\Auth\Exception\DeviceLimitExceededException;
use Src\Domain\Auth\Exception\OtpNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $exception) {
            if ($exception instanceof DeviceLimitExceededException) {
                return response()->json([
                    'status' => 'DEVICE_LIMIT_EXCEEDED',
                    'message' => $exception->getMessage(),
                    'device_token' => $exception->getDeviceToken(),
                ], 400);
            }

            if ($exception instanceof DeviceInvalidException) {
                return response()->json([
                    'status' => 'NEW_DEVICE',
                    'device_token' => $exception->getDeviceToken(),
                    'message' => $exception->getMessage()
                ], 400);
            }
            if ($exception instanceof OtpNotFoundException) {
                return response()->json([
                    'message' => 'Mã OTP không hợp lệ, vui lòng thử lại.'
                ], 422);
            }

            return response()->json([
                'message' => $exception->getMessage()
            ], $exception->getCode());
        });
    }
}

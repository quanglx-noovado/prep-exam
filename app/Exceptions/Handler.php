<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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

        });
    }

    public function render(
        $request,
        Throwable $e
    ): Response|JsonResponse|\Symfony\Component\HttpFoundation\Response|RedirectResponse {
        if ($e instanceof DeviceLimitExceededException) {
            return response()->json([
                'status' => 'DEVICE_LIMIT_EXCEEDED',
                'message' => $e->getMessage(),
                'device_token' => $e->getDeviceToken(),
            ], 400);
        }

        if ($e instanceof DeviceInvalidException) {
            return response()->json([
                'status' => 'NEW_DEVICE',
                'device_token' => $e->getDeviceToken(),
                'message' => $e->getMessage()
            ], 400);
        }
        if ($e instanceof OtpNotFoundException) {
            return response()->json([
                'message' => 'Mã OTP không hợp lệ, vui lòng thử lại.'
            ], 422);
        }

        return response()->json([
            'message' => $e->getMessage()
        ], $e->getCode());
    }
}

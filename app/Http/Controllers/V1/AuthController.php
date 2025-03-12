<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SendOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use League\Tactician\CommandBus;
use Src\Application\Auth\Command\LoginCommand;
use Src\Application\Auth\Command\SendOtpCommand;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;
use Src\Domain\Auth\Exception\AuthenticationException;
use Src\Domain\Auth\Exception\DeviceInvalidException;
use Src\Domain\Auth\Exception\DeviceLimitExceededException;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\UserNotFoundException;

class AuthController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus
    ) {

    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $command = new LoginCommand(
                email: $request->input('email'),
                password: $request->input('password'),
                deviceName: $request->input('device_name'),
                fingerPrint: $request->input('finger_print')
            );

            $token = $this->commandBus->handle($command);

            return response()->json([
                'token' => $token,
                'success' => true,
            ]);
        } catch (DeviceLimitExceededException $exception) {
            return response()->json([
                'status' => 'DEVICE_LIMIT_EXCEEDED',
                'message' => $exception->getMessage(),
                'device_token' => $exception->getDeviceToken(),
                'active_devices' => array_map(function (Device $device) {
                    return [
                        'id' => $device->getId(),
                        'name' => $device->getName(),
                        'last_login_at' => $device->getLastLoginAt()?->format('Y-m-d H:i:s'),
                        'device_token' => $device->getDeviceToken(),
                    ];
                }, $exception->getActiveDevices())
            ], 400);
        } catch (DeviceInvalidException $exception) {
            return response()->json([
                'status' => 'OTP_REQUIRED',
                'device_token' => $exception->getDeviceToken(),
                'message' => $exception->getMessage()
            ], 400);
        } catch (AuthenticationException $exception) {
            return response()->json([
                'status' => 'AUTHENTICATE_FAILED',
                'message' => $exception->getMessage()
            ], 401);
        }
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        try {
            $command = new SendOtpCommand(
                deviceToken: $request->input('device_token'),
                type: OtpSendType::from($request->input('send_type')),
                purpose: OtpPurpose::from($request->input('otp_purpose'))
            );

            $this->commandBus->handle($command);

            return response()->json([
                'success' => true,
            ]);
        } catch (DeviceNotFoundException|UserNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 404);
        }
    }
}

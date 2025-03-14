<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\SendOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use League\Tactician\CommandBus;
use Src\Application\Auth\Command\LoginCommand;
use Src\Application\Auth\Command\SendOtpCommand;
use Src\Application\Auth\Command\VerifyNewDeviceCommand;
use Src\Application\Auth\Command\VerifyRemoveDeviceCommand;
use Src\Domain\Auth\Entity\Device;
use Src\Domain\Auth\Enum\OtpPurpose;
use Src\Domain\Auth\Enum\OtpSendType;
use Src\Domain\Auth\Repository\DeviceRepository;

class AuthController extends BaseController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly DeviceRepository $deviceRepository
    ) {

    }

    public function login(LoginRequest $request): JsonResponse
    {
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
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $command = new SendOtpCommand(
            deviceToken: $request->input('device_token'),
            type: OtpSendType::from($request->input('send_type')),
            purpose: OtpPurpose::from($request->input('otp_purpose'))
        );

        $this->commandBus->handle($command);

        return response()->json([
            'success' => true,
        ]);
    }

    public function verifyNewDevice(Request $request): JsonResponse
    {
        $command = new VerifyNewDeviceCommand(
            deviceToken: $request->input('device_token'),
            otpCode: $request->input('otp_code')
        );

        $this->commandBus->handle($command);

        return response()->json([
            'success' => true,
        ]);
    }

    public function verifyRemoveDevice(Request $request): JsonResponse
    {
        $command = new VerifyRemoveDeviceCommand(
            deviceToken: $request->input('device_token'),
            otpCode: $request->input('otp_code'),
            removeDeviceTokens: $request->input('remove_device_tokens')
        );

        $this->commandBus->handle($command);

        return response()->json([
            'success' => true,
        ]);
    }

    public function getListActiveDevice(Request $request): JsonResponse
    {
        $device = $this->deviceRepository->getByDeviceToken($request->input('device_token'));
        $listActive = $this->deviceRepository->getListActiveDevice($device->getUserId());
        $data = array_map(static function (Device $device) {
            return [
                'id' => $device->getId(),
                'device_token' => $device->getDeviceToken(),
                'name' => $device->getName(),
                'is_active' => $device->isActive(),
                'last_login_at' => $device->getLastLoginAt()?->format('Y-m-d H:i:s'),
            ];
        }, $listActive);


        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

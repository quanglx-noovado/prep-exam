<?php

namespace Src\Application\Auth\Command;

use App\Helpers\StringHelper;
use App\Models\Device;
use Carbon\Carbon;
use Src\Application\Auth\Service\DeviceService;
use Src\Domain\Auth\AuthService;
use Src\Domain\Auth\Entity\Device as DeviceEntity;
use Src\Domain\Auth\Entity\User;
use Src\Domain\Auth\Exception\AuthenticationException;
use Src\Domain\Auth\Exception\DeviceInvalidException;
use Src\Domain\Auth\Exception\DeviceLimitExceededException;
use Src\Domain\Auth\Exception\DeviceNotFoundException;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\DeviceRepository;
use Src\Domain\Auth\Repository\UserRepository;

class LoginHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthService $authService,
        private readonly DeviceRepository $deviceRepository,
        private readonly DeviceService $deviceService,
    ) {
    }

    /**
     * @throws DeviceLimitExceededException
     * @throws AuthenticationException
     * @throws DeviceInvalidException
     * @throws UserNotFoundException
     */
    public function handle(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->email);
        $this->verifyCredential($command, $user);

        $fingerPrint = $this->generateFingerPrint($command);

        try {
            $device = $this->deviceRepository->getByUserAndFingerPrint($user->getId(), $fingerPrint);
            $this->updateDeviceToken($device);

            if ($device->getVerifiedAt() === null) {
                throw new DeviceInvalidException(
                    'Thiết bị chưa được xác thực. Vui lòng xác thực thiết bị.',
                    $device->getDeviceToken()
                );
            }

            if ($device->isActive()) {
                return $this->handleActiveDevice($user, $device);
            }

            return $this->handleInactiveDevice($user, $device);
        } catch (DeviceNotFoundException $exception) {
            $this->handleNewDevice($user, $command, $fingerPrint);
        }
    }

    private function generateFingerPrint(LoginCommand $command): string
    {
        if ($command->platform === 'web') {
            return $this->generateWebFingerprint($command);
        }

        return $this->generateMobileFingerprint($command);
    }

    private function generateWebFingerprint(LoginCommand $command): string
    {
        $data = [
            'user_agent' => $command->userAgent,
            'platform' => $command->platform,
        ];

        return StringHelper::generateFingerPrint($data);
    }

    private function generateMobileFingerprint(LoginCommand $command): string
    {
        $data = [
            'device_id' => $command->deviceId,
            'platform' => $command->platform,
        ];

        return StringHelper::generateFingerPrint($data);
    }

    private function updateDeviceToken(DeviceEntity $device): void
    {
        $deviceToken = StringHelper::createDeviceToken($device->getUserId(), $device->getFingerPrint());
        $device->updateDeviceToken($deviceToken);
        $this->deviceService->updateDevice($device);
    }

    private function handleActiveDevice(User $user, DeviceEntity $device): string
    {
        $device->updateLastLoginAt(Carbon::now());
        $this->deviceService->updateDevice($device);

        return $this->authService->generateToken($user);
    }

    /**
     * @throws DeviceLimitExceededException
     */
    private function handleInactiveDevice(User $user, DeviceEntity $device): string
    {
        $countDeviceActive = $this->deviceRepository->countActiveDevice($user->getId());

        if ($countDeviceActive >= Device::MAX_ACTIVE_DEVICES) {
            throw new DeviceLimitExceededException(
                'Maximum active devices reached. Please deactivate another device first.',
                $device->getDeviceToken()
            );
        }
        $device->updateIsActive(true);
        $device->updateLastLoginAt(Carbon::now());
        $this->deviceService->updateDevice($device);

        return $this->authService->generateToken($user);
    }

    /**
     * @throws DeviceInvalidException
     */
    private function handleNewDevice(User $user, LoginCommand $command, string $fingerPrint): never
    {
        $deviceToken = StringHelper::createDeviceToken($user->getId(), $fingerPrint);

        $device = new DeviceEntity(
            userId: $user->getId(),
            name: $command->deviceName,
            fingerPrint: $fingerPrint,
            deviceToken: $deviceToken,
            isActive: false,
            lastLoginAt: null,
            verifiedAt: null,
        );

        $this->deviceRepository->create($device);

        throw new DeviceInvalidException('New device detected. OTP verification required.', $deviceToken);
    }

    /**
     * @throws AuthenticationException
     */
    private function verifyCredential(LoginCommand $command, User $user): void
    {
        $verify = $this->authService->verifyPassword($user, $command->password);
        if (!$verify) {
            throw new AuthenticationException();
        }
    }
}


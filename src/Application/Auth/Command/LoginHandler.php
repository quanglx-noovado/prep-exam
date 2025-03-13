<?php

namespace Src\Application\Auth\Command;

use App\Helpers\StringHelper;
use App\Models\Device;
use Carbon\Carbon;
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
        private readonly DeviceRepository $deviceRepository
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

        try {
            $device = $this->deviceRepository->getByUserAndFingerPrint($user->getId(), $command->fingerPrint);
            $deviceToken = StringHelper::createDeviceToken($user->getId(), $device->getFingerPrint());
            $device->updateDeviceToken($deviceToken);
            $this->deviceRepository->update($device);

            if ($device->getVerifiedAt() === null) {
                throw new DeviceInvalidException('New device detected. OTP verification required.', 422, $deviceToken);
            }

            if ($device->isActive()) {
                return $this->handleActiveDevice($user, $device);
            }

            return $this->handleInactiveDevice($user, $device);
        } catch (DeviceNotFoundException $exception) {
            $this->handleNewDevice($user, $command);
        }
    }

    private function handleActiveDevice(User $user, DeviceEntity $device): string
    {
        $device->updateLastLoginAt(Carbon::now());
        $this->deviceRepository->update($device);

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
        $this->deviceRepository->update($device);

        return $this->authService->generateToken($user);
    }

    /**
     * @throws DeviceInvalidException
     */
    private function handleNewDevice(User $user, LoginCommand $command): never
    {
        $deviceToken = StringHelper::createDeviceToken($user->getId(), $command->fingerPrint);

        $device = new DeviceEntity(
            userId: $user->getId(),
            name: $command->deviceName,
            fingerPrint: $command->fingerPrint,
            deviceToken: $deviceToken,
            isActive: false,
            lastLoginAt: null,
            verifiedAt: null,
        );

        $this->deviceRepository->create($device);

        throw new DeviceInvalidException('New device detected. OTP verification required.', 422, $deviceToken);
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


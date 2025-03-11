<?php

namespace Src\Application\Auth\Command;

use Src\Domain\Auth\AuthService;
use Src\Domain\Auth\Exception\AuthenticationException;
use Src\Domain\Auth\User;
use Src\Domain\Auth\UserRepository;

class LoginHandler
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly AuthService $authService
    ) {
    }

    /**
     * @throws AuthenticationException
     */
    public function handle(LoginCommand $command): string
    {
        $user = $this->userRepository->findByEmail($command->email);
        $this->verifyCredential($command, $user);

        return $this->authService->generateToken($user);
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


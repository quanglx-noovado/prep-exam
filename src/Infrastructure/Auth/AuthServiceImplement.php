<?php

namespace Src\Infrastructure\Auth;

use App\Models\User;
use Src\Domain\Auth\AuthService;
use Src\Domain\Auth\Entity\User as UserEntity;

class AuthServiceImplement implements AuthService
{
    public function verifyPassword(UserEntity $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

    public function generateToken(UserEntity $user): string
    {
        $model = User::query()->find($user->getId());

        return $model->createToken('auth_token')->accessToken;
    }
}

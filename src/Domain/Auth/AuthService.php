<?php

namespace Src\Domain\Auth;

use Src\Domain\Auth\Entity\User;

interface AuthService
{
    public function verifyPassword(User $user, string $password): bool;

    public function generateToken(User $user): string;
}

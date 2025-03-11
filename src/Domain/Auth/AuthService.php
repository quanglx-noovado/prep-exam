<?php

namespace Src\Domain\Auth;

interface AuthService
{
    public function verifyPassword(User $user, string $password): bool;

    public function generateToken(User $user): string;
}

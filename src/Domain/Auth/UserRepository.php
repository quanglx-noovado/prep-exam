<?php

namespace Src\Domain\Auth;

interface UserRepository
{
    public function findByEmail(string $email): User;
}

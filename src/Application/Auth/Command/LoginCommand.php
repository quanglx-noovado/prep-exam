<?php

namespace Src\Application\Auth\Command;

class LoginCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {
    }
}

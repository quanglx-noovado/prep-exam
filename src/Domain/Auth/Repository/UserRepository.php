<?php

namespace Src\Domain\Auth\Repository;

use Src\Domain\Auth\Entity\User;
use Src\Domain\Auth\Exception\UserNotFoundException;

interface UserRepository
{
    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): User;

    /**
     * @throws UserNotFoundException
     */
    public function getById(int $id): User;
}

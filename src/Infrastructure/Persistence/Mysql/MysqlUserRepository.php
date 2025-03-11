<?php

namespace Src\Infrastructure\Persistence\Mysql;

use App\Models\User;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\User as UserEntity;
use Src\Domain\Auth\UserRepository;

class MysqlUserRepository implements UserRepository
{
    /**
     * @param string $email
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): UserEntity
    {
        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        $entity = new UserEntity(
            email: $user->email,
            password: $user->password,
            name: $user->name
        );
        $entity->setId($user->id);

        return $entity;
    }
}

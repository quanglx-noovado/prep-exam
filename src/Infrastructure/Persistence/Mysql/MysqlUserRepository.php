<?php

namespace Src\Infrastructure\Persistence\Mysql;

use App\Models\User;
use Src\Domain\Auth\Entity\User as UserEntity;
use Src\Domain\Auth\Exception\UserNotFoundException;
use Src\Domain\Auth\Repository\UserRepository;

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

        return $this->buildEntity($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function getById(int $id): UserEntity
    {
        $user = User::query()->where('id', $id)->first();

        if (!$user) {
            throw new UserNotFoundException();
        }

        return $this->buildEntity($user);
    }

    private function buildEntity(User $user): UserEntity
    {
        $entity = new UserEntity(
            email: $user->email,
            password: $user->password,
            name: $user->name
        );
        $entity->setId($user->id);

        return $entity;
    }
}

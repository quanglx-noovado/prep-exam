<?php

namespace Src\Domain\Auth\Entity;

class User
{
    private ?int $id;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly string $name
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

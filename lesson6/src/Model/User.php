<?php

namespace src\Model;

class User {
    public function __construct(
        private UUID $uuid,
        private string $username,
        private string $hashedPassword,
        private Name $name
    ) {
    }

    public function getUuid(): UUID {
        return $this->uuid;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getHashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $password . $uuid);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->getUuid());
    }

    public function getName(): Name {
        return $this->name;
    }

    public static function createForm(
        string $username,
        string $password,
        Name $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $username,
            self::hash($password, $uuid),
            $name
        );
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
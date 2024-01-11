<?php
namespace src\Model;
use Ramsey\Uuid\Uuid as RamseyUuid;
use src\Exceptions\InvalidArgumentException;

class UUID {
    public function __construct(
        private string $uuid
    ) {
        if (!RamseyUuid::isValid($this->uuid))
            throw new InvalidArgumentException('Не корректный UUID');
    }

    public function __toString(): string {
        return $this->uuid;
    }

    public static function random(): self {
        return new self(RamseyUuid::uuid4()->toString());
    }
}
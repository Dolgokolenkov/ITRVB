<?php

namespace src\Commands;

use src\Exceptions\ArgumentsException;

class Arguments {
    public function __construct(
        private array $arguments
    ) {
        foreach ($this->arguments as $argument => $value) {
            $stringValue = trim((string)$value);

            if (empty($stringValue))
                continue;

            $this->arguments[(string)$argument] = $stringValue;
        }
    }

    public static function fromArgv(array $argv): self {
        $arguments = [];

        foreach ($argv as $argument) {
            $parts = explode('=', $argument);

            if (count($parts) !== 2)
                continue;

            $arguments[$parts[0]] = $parts[1];
        }

        return new self($arguments);
    }

    public function get(string $argument): string {
        if (!array_key_exists($argument, $this->arguments)) {
            throw new ArgumentsException('Аргумент не найдент');
        }

        return $this->arguments[$argument];
    }
}
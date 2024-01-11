<?php

namespace src\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use src\Exceptions\ContainerNotFoundException;

class DIContainer implements ContainerInterface
{
    private array $resolves = [];

    public function has(string $type): bool
    {
        try {
            $this->get($type);
        } catch (ContainerNotFoundException) {
            return false;
        }

        return true;
    }

    public function bind(string $type, $class)
    {
        $this->resolves[$type] = $class;
    }

    public function get(string $type): object
    {
        if (!array_key_exists($type, $this->resolves)) {
            if (class_exists($type)) {
                $reflectionClass = new ReflectionClass($type);
                $constructor = $reflectionClass->getConstructor();

                if ($constructor === null) {
                    return new $type();
                }

                $parameters = [];

                foreach ($constructor->getParameters() as $parameter) {
                    $parameterType = $parameter->getType()->getName();
                    $parameters[] = $this->get($parameterType);
                }

                return new $type(...$parameters);
            }

            throw new ContainerNotFoundException("Cannot resolve type: $type");
        }

        $typeToCreate = $this->resolves[$type];

        if (is_object($typeToCreate)) {
            return $typeToCreate;
        }

        return $this->get($typeToCreate);
    }
}
<?php

namespace tests\Container;

use PHPUnit\Framework\TestCase;
use src\Container\DIContainer;
use src\Exceptions\ContainerNotFoundException;
use src\Repositories\InMemoryUserRepository;
use src\Repositories\UserRepository;
use src\Repositories\UserRepositoryInterface;

class DIContainerTests extends TestCase
{
    public function testItThrowCannotResolveType(): void
    {
        $container = new DIContainer();

        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionMessage('Cannot resolve type: tests\Container\NonExistentClass');

        $container->get('tests\Container\NonExistentClass');
    }

    public function testItResolvesClassWithoutDependencies(): void
    {
        $container = new DIContainer();
        $object = $container->get(SomeClassWithoutDependencies::class);
        $this->assertInstanceOf(SomeClassWithoutDependencies::class, $object);
    }

    public function testItResolvesClassByContract(): void
    {
        $container = new DIContainer();

        $container->bind(UserRepositoryInterface::class, InMemoryUserRepository::class);

        $object = $container->get(UserRepositoryInterface::class);

        $this->assertInstanceOf(InMemoryUserRepository::class, $object);
    }

    public function testItReturnPredefinedObject(): void
    {
        $container = new DIContainer();

        $container->bind(SomeClassWithParameter::class, new SomeClassWithParameter(444));

        $object = $container->get(SomeClassWithParameter::class);

        $this->assertInstanceOf(SomeClassWithParameter::class, $object);

        $this->assertSame(444, $object->getValue());
    }

    public function testItResolvesClassWithDependencies(): void
    {
        $container = new DIContainer;

        $container->bind(SomeClassWithParameter::class, new SomeClassWithParameter(123));

        $object = $container->get(ClassDependingOnAnother::class);

        $this->assertInstanceOf(ClassDependingOnAnother::class, $object);
    }
}
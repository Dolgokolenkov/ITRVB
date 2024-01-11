<?php

namespace tests\Commands;

use PHPUnit\Framework\TestCase;
use src\Commands\Arguments;
use src\Commands\CreateUserCommand;
use src\Exceptions\ArgumentsException;
use src\Exceptions\CommandException;
use src\Exceptions\UserNotFoundException;
use src\Model\Name;
use src\Model\User;
use src\Model\UUID;
use src\Repositories\UserRepositoryInterface;
use tests\DummyLogger;

class CreateUserCommandTests extends TestCase
{
    private $userRepository;
    private $createUserCommand;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->createUserCommand = new CreateUserCommand($this->userRepository, new DummyLogger());
    }

    public function testHandleCreatesUserWhenNotExists(): void
    {
        $this->userRepository
            ->method('getByUsername')
            ->will($this->throwException(new UserNotFoundException()));

        $arguments = $this->createMock(Arguments::class);
        $arguments->method('get')
            ->willReturnMap([
                ['username', 'testuser'],
                ['password', '123'],
                ['first_name', 'Test'],
                ['last_name', 'User']
            ]);

        $this->userRepository
            ->expects($this->once())
            ->method('save');

        $this->createUserCommand->handle($arguments);

        $this->assertTrue(true);
    }

    public function testHandleThrowsExceptionWhenUserExists(): void
    {
        $this->expectException(CommandException::class);
        $this->expectExceptionMessage("User already exists: testuser");

        $username = 'testuser';
        $password = '123';
        $uuid = UUID::random();
        $this->userRepository
            ->method('getByUsername')
            ->willReturn(new User(
                $uuid,
                $username,
                $password,
                new Name(
                    "firstName",
                    "lastName"
                )
            ));

        $arguments = $this->createMock(Arguments::class);
        $arguments->method('get')
            ->willReturn($username);

        $this->createUserCommand->handle($arguments);
    }

    public function testItPequiewsPassword(): void
    {
        $this->expectException(ArgumentsException::class);
        $this->expectExceptionMessage('Аргумент не найдент');

        $this->createUserCommand->handle(new Arguments(['username' => 'Ivan']));
    }
}
<?php

namespace tests\Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\Exceptions\UserNotFoundException;
use src\Model\Name;
use src\Model\User;
use src\Model\UUID;
use src\Repositories\UserRepository;
use tests\DummyLogger;

class UserRepositoryTests extends TestCase
{
    private $pdoMock;
    private $stmtMock;
    private $repo;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->repo = new UserRepository($this->pdoMock, new DummyLogger());
    }

    public function testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot get user: Ivan');

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetch')->willReturn(false);

        $this->repo->getByUsername('Ivan');
    }

    public function testItSaveUserToDatabase(): void
    {
        $uuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetchColumn')->willReturn(0);
        $this->stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturn(true, true);

        $this->repo->save(
            new User(
                $uuid,
                'ivan123',
                '123',
                new Name('ivan', 'ivanov')
            )
        );
    }

    public function testGetUser(): void {
        $uuid = UUID::random();

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn([
            'uuid' => $uuid,
            'username' => 'ivan123',
            'password' => 'ivan123',
            'first_name' => 'ivan',
            'last_name' => 'ivanov',
        ]);

        $user = $this->repo->get($uuid);

        $this->assertNotNull($user);
        $this->assertEquals($uuid, $user->getUuid());
    }

    public function testGetUserByName(): void {
        $username = "ivan123";

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn([
            'uuid' => UUID::random(),
            'username' => $username,
            'password' => $username,
            'first_name' => 'ivan',
            'last_name' => 'ivanov',
        ]);

        $user = $this->repo->getByUsername($username);

        $this->assertNotNull($user);
        $this->assertEquals($username, $user->getUsername());
    }

}
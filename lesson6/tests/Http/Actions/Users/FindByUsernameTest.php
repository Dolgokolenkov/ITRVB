<?php

namespace tests\Http\Actions\Users;

use myHttp\Actions\Users\FindByUsername;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\SuccessfullResponse;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\Model\UUID;
use src\Repositories\UserRepository;
use tests\DummyLogger;

class FindByUsernameTest extends TestCase
{
    private $pdoMock;
    private $stmtMock;
    private UserRepository $repo;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->repo = new UserRepository($this->pdoMock, new DummyLogger());
    }

    public function testItReturnErrorIfParamUserNotFound(): void
    {
        $request = new Request([], [], []);
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetch')->willReturn(false);

        $action = new FindByUsername($this->repo);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"succuess":false,"reason":"Incorrect param for query"}');
        $response->send();
    }

    public function testItReturnErrorIfUserNotFound(): void
    {
        $request = new Request(['username' => 'Ivan'], [], []);
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetch')->willReturn(false);

        $action = new FindByUsername($this->repo);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"succuess":false,"reason":"Cannot get user: Ivan"}');
        $response->send();
    }

    public function testItReturnUserByName(): void
    {
        $uuid = new UUID('c89457cb-27b6-4ed4-bc90-503f1b47a2dc');

        $mockUserData = [
            'uuid' => $uuid,
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Ivanov',
            'password' => '123',
        ];

        $request = new Request(['username' => 'Ivan'], [], []);
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetch')->willReturn($mockUserData);

        $action = new FindByUsername($this->repo);
        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfullResponse::class, $response);
        $this->expectOutputString('{"succuess":true,"data":{"username":"ivan123","name":"Ivan Ivanov"}}');

        $response->send();
    }
}
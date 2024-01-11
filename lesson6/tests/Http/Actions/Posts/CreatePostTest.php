<?php

namespace tests\Http\Actions\Posts;

use myHttp\Actions\Posts\CreatePost;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\SuccessfullResponse;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\Exceptions\PostIncorrectDataException;
use src\Model\UUID;
use src\Repositories\PostRepository;
use tests\DummyLogger;
use tests\DummyTokenAuth;

class CreatePostTest extends TestCase
{
    private PDO $pdoMock;
    private PDOStatement $stmtMock;
    private PostRepository $postRepository;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->postRepository = new PostRepository($this->pdoMock, new DummyLogger());
    }

    public function testItSuccess(): void
    {
        $request = new Request(
            [],
            ['author_uuid' => UUID::random(), 'title' => 'Test Title', 'text' => 'Test Text'],
            []
        );

       $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
       $this->stmtMock->method('execute')->willReturn(true);
       $this->stmtMock->method('fetchColumn')->willReturn(10);
       $createPostAction = new CreatePost($this->postRepository, new DummyTokenAuth());
       $response = $createPostAction->handle($request);
       $this->assertInstanceOf(SuccessfullResponse::class, $response);
    }

    public function testItIncorrectUuid(): void
    {
        $request = new Request(
            [],
            ['author_uuid' => 'incorrect_uuid', 'title' => 'Test Title', 'text' => 'Test Text'],
            []
        );

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $createPostAction = new CreatePost($this->postRepository, new DummyTokenAuth());
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);
    }

    public function testItIncorrectUuidAuthor(): void
    {
        $uuid = new UUID('c89457cb-27b6-4ed4-bc90-503f1b47a2dc');
        $request = new Request(
            [],
            ['author_uuid' => $uuid, 'title' => 'Test Title', 'text' => 'Test Text'],
            []
        );

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('fetchColumn')->willReturn(0);
        $this->stmtMock->method('execute')->willReturn(true);
        $createPostAction = new CreatePost($this->postRepository, new DummyTokenAuth());
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $responseBody = $response->getBody();
        $responseBodyString = json_encode(json_decode($responseBody), JSON_UNESCAPED_UNICODE);
        $this->assertSame('"Author with UUID '.$uuid.' not found"', $responseBodyString);
    }

    public function testItEmptyTitle(): void
    {
        $request = new Request(
            [],
            ['author_uuid' => UUID::random(), 'text' => 'Test Text'],
            []
        );
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $createPostAction = new CreatePost($this->postRepository, new DummyTokenAuth());
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $responseBody = $response->getBody();
        $responseBodyString = json_encode(json_decode($responseBody), JSON_UNESCAPED_UNICODE);
        $this->assertSame('"Incorrect param for body: title"', $responseBodyString);
    }

    public function testItEmptyText(): void
    {
        $request = new Request(
            [],
            ['author_uuid' => 'Test Title', 'title' => 'Test Text'],
            []
        );
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(false);
        $createPostAction = new CreatePost($this->postRepository, new DummyTokenAuth());
        $response = $createPostAction->handle($request);
        $this->assertInstanceOf(ErrorResponse::class, $response);

        $responseBody = $response->getBody();
        $responseBodyString = json_encode(json_decode($responseBody), JSON_UNESCAPED_UNICODE);
        $this->assertSame('"Incorrect param for body: text"', $responseBodyString);
    }
}
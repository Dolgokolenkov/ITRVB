<?php

namespace tests\Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\Exceptions\CommentNotFoundException;
use src\Model\Comment;
use src\Model\UUID;
use src\Repositories\CommentRepository;
use tests\DummyLogger;

class CommentRepositoryTests extends TestCase
{
    private $pdoMock;
    private $stmtMock;
    private $repo;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->repo = new CommentRepository($this->pdoMock, new DummyLogger());
    }

    public function testSaveComment(): void {
        $uuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $authorUuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $articleUuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $text = 'Test Text';
        $comment = new Comment($uuid, $authorUuid, $articleUuid, $text);

        $this->pdoMock->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->repo->save($comment);
    }

    public function testFindCommentByUuid(): void {
        $uuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $authorUuid = new UUID('410f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $articleUuid = new UUID('710f3bc9-aa9d-4b5b-a00c-292c4c5dc729');
        $text = 'Test Text';

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn([
            'uuid' => $uuid,
            'author_uuid' => $authorUuid,
            'post_uuid' => $articleUuid,
            'text' => $text
        ]);

        $comment = $this->repo->get($uuid);

        $this->assertNotNull($comment);
        $this->assertEquals($uuid, $comment->getUuid());
    }

    public function testThrowsExceptionIfCommentNotFound(): void {
        $nonExistentUuid = new UUID('310f3bc9-aa9d-4b5b-a00c-292c4c5dc729');

        $this->expectException(CommentNotFoundException::class);
        $this->expectExceptionMessage("Comment with UUID $nonExistentUuid not found");

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn(null);

        $this->repo->get($nonExistentUuid);
    }
}
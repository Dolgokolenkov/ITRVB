<?php

namespace tests\Repositories;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use src\Exceptions\PostNotFoundException;
use src\Model\Post;
use src\Model\UUID;
use src\Repositories\PostRepository;
use tests\DummyLogger;

class ArticleRepositoryTests extends TestCase
{
    private PDO $pdoMock;
    private PDOStatement $stmtMock;
    private PostRepository $repo;

    protected function setUp(): void {
        $this->pdoMock = $this->createMock(PDO::class);
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->repo = new PostRepository($this->pdoMock, new DummyLogger());
    }

    public function testSaveArticle(): void {
        $uuid = new UUID('e91233da-fadf-40ef-8b82-f91969e700c9');
        $authorUuid = new UUID('e91233da-cbdf-40ef-8b82-f91969e700c9');
        $article = new Post($uuid, $authorUuid, 'Test Title', 'Test Text');

        $this->pdoMock->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock->expects($this->exactly(2))
            ->method('execute')
            ->willReturnOnConsecutiveCalls(true, true);

        $this->stmtMock->method('fetchColumn')
            ->willReturn(1);

        $this->repo->save($article);
    }

    public function testFindArticleByUuid(): void {
        $uuid = UUID::random();
        $authorUuid = UUID::random();

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn([
            'uuid' => $uuid,
            'author_uuid' => $authorUuid,
            'title' => 'Test Title',
            'text' => 'Test Text'
        ]);

        $article = $this->repo->get($uuid);

        $this->assertNotNull($article);
        $this->assertEquals($uuid, $article->getUuid());
    }

    public function testThrowsExceptionIfArticleNotFound(): void {
        $nonExistentUuid = UUID::random();

        $this->expectException(PostNotFoundException::class);
        $this->expectExceptionMessage("Post with UUID $nonExistentUuid not found");

        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn(false);

        $this->repo->get($nonExistentUuid);
    }
}
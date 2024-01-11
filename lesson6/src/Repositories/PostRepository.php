<?php
namespace src\Repositories;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use src\Exceptions\PostIncorrectDataException;
use src\Exceptions\PostNotFoundException;
use src\Model\Post;
use src\Model\UUID;

class PostRepository implements PostsRepositoryInterface {

    public function __construct(
        private PDO $pdo,
        private LoggerInterface $logger
    ) { }

    public function get(UUID $uuid): Post
    {
        $stmt = $this->pdo->prepare("SELECT * FROM posts WHERE uuid = :uuid");

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $this->logger->warning("Post not found", ['uuid' => $uuid]);
                throw new PostNotFoundException("Post with UUID $uuid not found");
            }
        } catch (PDOException $e) {
            throw new PostIncorrectDataException("Error when get post: " . $e->getMessage());
        }

        $this->logger->info("Post get successfully", ['uuid' => $uuid]);
        return new Post($result['uuid'], $result['author_uuid'],
            $result['title'], $result['text']);
    }

    public function save(Post $post): void {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE uuid = :uuid");
        $stmt->execute([':uuid' => $post->getAuthorUuid()]);
        if ($stmt->fetchColumn() == 0) {
            $this->logger->warning("Author not found", ['authorUuid' => $post->getAuthorUuid()]);
            throw new PostIncorrectDataException("Author with UUID {$post->getAuthorUuid()} not found");
        }

        $stmt = $this->pdo->prepare("INSERT INTO posts (uuid, author_uuid, title, text) 
            VALUES (:uuid, :author_uuid, :title, :text)");

        try {
            $stmt->execute([
                ':uuid' => (string) $post->getUuid(),
                ':author_uuid' => (string) $post->getAuthorUuid(),
                ':title' => $post->getTitle(),
                ':text' => $post->getText()
            ]);
            $this->logger->info("Post saved successfully", ['uuid' => $post->getUuid()]);
        } catch (PDOException $e) {
            throw new PostIncorrectDataException("Error when save post: " . $e->getMessage());
        }
    }

    public function delete(UUID $uuid): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE uuid = :uuid");
        $stmt->execute([':uuid' => $uuid]);

        if ($stmt->rowCount() === 0) {
            $this->logger->warning("Post not found", ['uuid' => $uuid]);
            throw new PostNotFoundException("Post with UUID $uuid not found");
        } else {
            $this->logger->info("Post delete successfully", ['uuid' => $uuid]);
        }
    }
}
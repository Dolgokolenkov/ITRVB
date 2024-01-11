<?php
namespace src\Repositories;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use src\Exceptions\CommentIncorrectDataException;
use src\Exceptions\CommentNotFoundException;
use src\Model\Comment;
use src\Model\UUID;

class CommentRepository implements CommentsRepositoryInterface {
    public function __construct(
        private PDO $pdo,
        private LoggerInterface $logger
    ) {
    }

    public function get(UUID $uuid): Comment
    {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE uuid = :uuid");

        try {
            $stmt->execute([
                ":uuid" => $uuid
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                $this->logger->warning("Comment not found", ['uuid' => $uuid]);
                throw new CommentNotFoundException("Comment with UUID $uuid not found");
            }
        } catch (PDOException $e) {
            throw new CommentIncorrectDataException("Error when comment get: " . $e->getMessage());
        }

        $this->logger->info("Comment get successfully", ['uuid' => $uuid]);
        return new Comment($result['uuid'], $result['author_uuid'],
            $result['post_uuid'], $result['text']);
    }

    public function save(Comment $comment): void {
        $stmt = $this->pdo->prepare("INSERT INTO comments (uuid, author_uuid, post_uuid, text) 
            VALUES (:uuid, :author_uuid, :post_uuid, :text)");

        try {
            $stmt->execute([
                ':uuid' => $comment->getUuid(),
                ':author_uuid' => $comment->getAuthorUuid(),
                ':post_uuid' => $comment->getPostUuid(),
                ':text' => $comment->getText()
            ]);
            $this->logger->info("Comment saved successfully", ['uuid' => $comment->getUuid()]);
        } catch (PDOException $e) {
            $this->logger->warning("Comment not saved", ['uuid' => $comment->getUuid()]);
            throw new CommentIncorrectDataException("Error when comment save: " . $e->getMessage());
        }
    }
}
<?php

namespace src\Repositories;

use PDO;
use PDOException;
use Psr\Log\LoggerInterface;
use src\Exceptions\PostIncorrectDataException;
use src\Exceptions\PostLikeAlreadyExistsException;
use src\Exceptions\PostLikeNotFoundException;
use src\Model\PostLike;
use src\Model\UUID;

class PostLikeRepository implements PostLikeRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
        private LoggerInterface $logger
    ) { }

    public function save(PostLike $postLike)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE uuid = :post_uuid");
        $stmt->execute([':post_uuid' => $postLike->getPostUuid()]);
        if ($stmt->fetchColumn() == 0) {
            $this->logger->warning("Post like not found", ['uuid' => $postLike->getPostUuid()]);
            throw new PostIncorrectDataException("Post with UUID 
                {$postLike->getPostUuid()} not found");
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE uuid = :user_uuid");
        $stmt->execute([':user_uuid' => $postLike->getUserUuid()]);
        if ($stmt->fetchColumn() == 0) {
            $this->logger->warning("User not found", ['uuid' => $postLike->getUserUuid()]);
            throw new PostIncorrectDataException("User with UUID 
                {$postLike->getUserUuid()} not found");
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE 
                                    post_uuid = :post_uuid AND user_uuid = :user_uuid");
        $stmt->execute([
            ':post_uuid' => $postLike->getPostUuid(),
            ':user_uuid' => $postLike->getUserUuid()
        ]);
        if ($stmt->fetchColumn() > 0) {
            $this->logger->warning("Like from user to post already exists",
                ['userUuid' => $postLike->getUserUuid(),
                    'postUuid' => $postLike->getPostUuid()]);
            throw new PostLikeAlreadyExistsException("Like from user UUID 
                {$postLike->getUserUuid()} to post UUID {$postLike->getPostUuid()} already exists");
        }

        $stmt = $this->pdo->prepare("INSERT INTO post_likes (uuid, post_uuid, user_uuid) 
                    VALUES (:uuid, :post_uuid, :user_uuid)");

        try {
            $stmt->execute([
                ':uuid' => $postLike->getUuid(),
                ':post_uuid' => $postLike->getPostUuid(),
                ':user_uuid' => $postLike->getUserUuid(),
            ]);
            $this->logger->info("Post like saved successfully", ['uuid' => $postLike->getUuid()]);
        } catch (PDOException $e) {
            throw new PostIncorrectDataException("Incorrect to save comment like: " .
                $e->getMessage());
        }
    }

    public function getByPostUuid(UUID $postUuid): array
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts WHERE uuid = :post_uuid");
        $stmt->execute([':post_uuid' => $postUuid]);
        if ($stmt->fetchColumn() == 0) {
            $this->logger->warning("Post not found", ['uuid' => $postUuid]);
            throw new PostLikeNotFoundException("Post with UUID 
                {$postUuid} not found");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM post_likes WHERE post_uuid = :post_uuid");

        try {
            $stmt->execute([':post_uuid' => $postUuid]);

            $likes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $likes[] = new PostLike(
                    new UUID($row['uuid']),
                    new UUID($row['post_uuid']),
                    new UUID($row['user_uuid'])
                );
            }
            $this->logger->info("Post likes get successfully", ['postUuid' => $postUuid]);
        } catch (\PDOException) {
            throw new PostLikeNotFoundException('Comment like not found');
        }

        return $likes;
    }
}
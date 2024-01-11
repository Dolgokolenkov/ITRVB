<?php

namespace src\Repositories;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use PDO;
use PDOException;
use src\Exceptions\AuthTokenRepositoryException;
use src\Model\AuthToken;
use src\Model\UUID;
use src\Repositories\AuthTokenRepositoryInterface;

class AuthTokenRepository implements AuthTokenRepositoryInterface
{
    public function __construct(
        private PDO $connection
    ) { }

    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
			INSERT INTO tokens (token, user_uuid, expires_on)
			VALUES (:token, :user_uuid, :expires_on)
			ON CONFLICT (token) DO UPDATE SET
			expires_on = :expires_on
SQL;

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute([
                ':token' => (string)$authToken,
                ':user_uuid' => (string)$authToken->getUuid(),
                ':expires_on' => $authToken->getExpiresOn()->format(DateTimeInterface::ATOM)
            ]);
        } catch (PDOException $error) {
            throw new AuthTokenRepositoryException(
                $error->getMessage(), (int)$error->getCode(), $error
            );
        }
    }

    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare('SELECT * FROM tokens WHERE token = :token');
            $statement->execute([':token' => $token]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            throw new AuthTokenRepositoryException(
                $error->getMessage(), (int)$error->getCode(), $error
            );
        }

        if ($result === false) {
            throw new AuthTokenRepositoryException(
                "Cannot find token: $token"
            );
        }

        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (Exception $error) {
            throw new AuthTokenREpositoryException(
                $error->getMessage(), (int)$error->getCode(), $error
            );
        }
    }

    public function reset(string $token): void
    {
        try {
            $statement = $this->connection->prepare('
                UPDATE tokens 
                SET expires_on = :expires_on 
                WHERE token = :token
            ');
            $statement->execute([
                ':token' => $token,
                ':expires_on' => (new DateTimeImmutable('-1 day'))->format(DateTimeInterface::ATOM)
            ]);

            if ($statement->rowCount() === 0) {
                throw new AuthTokenRepositoryException("Token not found or already expired: $token");
            }
        } catch (PDOException $error) {
            throw new AuthTokenRepositoryException(
                $error->getMessage(), (int)$error->getCode(), $error
            );
        }
    }

}
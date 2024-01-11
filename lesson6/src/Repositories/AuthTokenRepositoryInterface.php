<?php

namespace src\Repositories;

use src\Model\AuthToken;

interface AuthTokenRepositoryInterface
{
    public function save(AuthToken $authToken): void;
    public function get(string $token): AuthToken;
    public function reset(string $token): void;
}
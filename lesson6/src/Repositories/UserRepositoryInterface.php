<?php

namespace src\Repositories;

use src\Model\User;
use src\Model\UUID;

interface UserRepositoryInterface {
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}
<?php
namespace src\Repositories;

use src\Model\Comment;
use src\Model\UUID;

interface CommentsRepositoryInterface {
    public function get(UUID $uuid): Comment;
    public function save(Comment $comment): void;
}
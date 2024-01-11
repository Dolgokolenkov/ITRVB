<?php

namespace src\Repositories;

use src\Model\Post;
use src\Model\UUID;

interface PostsRepositoryInterface {
    public function get(UUID $uuid): Post;
    public function save(Post $post): void;
}
?>
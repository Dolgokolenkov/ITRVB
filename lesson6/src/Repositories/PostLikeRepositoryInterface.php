<?php

namespace src\Repositories;

use src\Model\PostLike;
use src\Model\UUID;

interface PostLikeRepositoryInterface
{
    public function save(PostLike $postLike);
    public function getByPostUuid(UUID $postUuid);
}
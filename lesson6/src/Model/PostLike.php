<?php

namespace src\Model;

class PostLike
{
    public function __construct(
        private UUID $uuid,
        private UUID $post_uuid,
        private UUID $user_uuid,
    ) { }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getPostUuid(): UUID
    {
        return $this->post_uuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->user_uuid;
    }
}
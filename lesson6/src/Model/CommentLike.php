<?php

namespace src\Model;

class CommentLike
{
    public function __construct(
        private UUID $uuid,
        private UUID $comment_uuid,
        private UUID $user_uuid,
    ) { }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getCommentUuid(): UUID
    {
        return $this->comment_uuid;
    }

    public function getUserUuid(): UUID
    {
        return $this->user_uuid;
    }
}
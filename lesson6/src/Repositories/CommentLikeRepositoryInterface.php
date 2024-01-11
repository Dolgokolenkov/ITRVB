<?php
namespace src\Repositories;

use src\Model\CommentLike;
use src\Model\UUID;

interface CommentLikeRepositoryInterface
{
    public function save(CommentLike $commentLike);
    public function getByCommentUuid(UUID $commentUuid);
}
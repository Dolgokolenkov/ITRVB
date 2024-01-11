<?php

namespace myHttp\Actions\Likes;

use myHttp\Actions\ActionInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Model\CommentLike;
use src\Model\UUID;
use src\Repositories\CommentLikeRepository;
use src\Repositories\CommentLikeRepositoryInterface;

class CreateCommentLike implements ActionInterface
{
    public function __construct(
      private CommentLikeRepositoryInterface $commentLikeRepository
    ) { }

    public function handle(Request $request): Response
    {
        try {
            $data = $request->body(['comment_uuid', 'user_uuid']);
            $uid = UUID::random();
            $userU = new UUID($data['user_uuid']);
            $commentU = new UUID($data['comment_uuid']);
            $comment = new CommentLike($uid, $commentU, $userU);
            $this->commentLikeRepository->save($comment);
            return new SuccessfullResponse(['message' => 'Post like created successfully']);
        } catch (\Exception $ex) {
            return new ErrorResponse($ex->getMessage());
        }
    }
}
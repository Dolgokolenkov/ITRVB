<?php

namespace myHttp\Actions\Likes;

use myHttp\Actions\ActionInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\HttpException;
use src\Exceptions\PostLikeNotFoundException;
use src\Model\PostLike;
use src\Model\UUID;
use src\Repositories\PostLikeRepository;
use src\Repositories\PostLikeRepositoryInterface;

class GetByUuidPostLikes implements ActionInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository
    )
    {

    }
    public function handle(Request $request): Response
    {
        try {
            $postUuid = $request->query('uuid');
        } catch (HttpException $ex) {
            return new ErrorResponse($ex->getMessage());
        }

        try {
            $postLikes = $this->postLikeRepository->getByPostUuid(new UUID($postUuid));
        } catch (PostLikeNotFoundException $ex) {
            return new ErrorResponse($ex->getMessage());
        }

        $likesData = array_map(function (PostLike $like) {
            return [
                'uuid' => (string)$like->getUuid(),
                'user_uuid' => (string)$like->getUserUuid()
            ];
        }, $postLikes);

        return new SuccessfullResponse([
            'post' => $postUuid,
            'likes' => $likesData
        ]);
    }
}
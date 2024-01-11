<?php

namespace myHttp\Actions\Likes;

use myHttp\Actions\ActionInterface;
use myHttp\Auth\TokenAuthInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\AuthException;
use src\Model\PostLike;
use src\Model\UUID;
use src\Repositories\PostLikeRepository;
use src\Repositories\PostLikeRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public function __construct(
        private PostLikeRepositoryInterface $postLikeRepository,
        private TokenAuthInterface $auth
    )
    {}

    public function handle(Request $request): Response
    {
        try {
            $data = $request->body(['post_uuid']);
            try {
                $user = $this->auth->user($request);
            } catch (AuthException $ex) {
                return new ErrorResponse($ex->getMessage());
            }
            $uid = UUID::random();
            $userU = $user->getUuid();
            $postU = new UUID($data['post_uuid']);
            $post = new PostLike($uid, $postU, $userU);
            $this->postLikeRepository->save($post);
            return new SuccessfullResponse(['message' => 'Post like created successfully']);
        } catch (\Exception $ex) {
            return new ErrorResponse($ex->getMessage());
        }
    }
}
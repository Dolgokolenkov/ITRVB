<?php

namespace myHttp\Actions\Posts;

use myHttp\Actions\ActionInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\HttpException;
use src\Repositories\PostsRepositoryInterface;

class DeletePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postRepository
    ) { }

    public function handle(Request $request): Response
    {
        try {
            $uuid = $request->query('uuid');
            $this->postRepository->delete($uuid);
            return new SuccessfullResponse(['message' => 'Post deleted successfully']);
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
    }
}
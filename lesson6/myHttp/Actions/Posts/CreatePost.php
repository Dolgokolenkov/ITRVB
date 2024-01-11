<?php

namespace myHttp\Actions\Posts;

use myHttp\Actions\ActionInterface;
use myHttp\Auth\AuthInterface;
use myHttp\Auth\TokenAuthInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\AuthException;
use src\Exceptions\InvalidArgumentException;
use src\Model\Post;
use src\Model\UUID;
use src\Repositories\PostsRepositoryInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postRepository,
        private TokenAuthInterface $auth
    ) { }
    public function handle(Request $request): Response
    {
        try {
            $user = $this->auth->user($request);
        } catch (AuthException $ex) {
            return new ErrorResponse($ex->getMessage());
        }

        try {
            $data = $request->body(['title', 'text']);
            $uuid = UUID::random();
            $authorUuid = $user->getUuid();
            $title = $data['title'];
            $text = $data['text'];

            if (empty($title) || empty($text)) {
                throw new InvalidArgumentException('Title or text cannot be empty');
            }

            $post = new Post($uuid, $authorUuid, $title, $text);
            $this->postRepository->save($post);

            return new SuccessfullResponse(['message' => 'Post created successfully']);
        } catch (\Exception $ex) {
            return new ErrorResponse($ex->getMessage());
        }
    }
}
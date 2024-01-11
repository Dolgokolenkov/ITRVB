<?php

namespace myHttp\Actions\Comments;

use myHttp\Actions\ActionInterface;
use myHttp\Auth\TokenAuthInterface;
use myHttp\ErrorResponse;
use myHttp\Request;
use myHttp\Response;
use myHttp\SuccessfullResponse;
use src\Exceptions\AuthException;
use src\Model\Comment;
use src\Model\UUID;
use src\Repositories\CommentRepository;
use src\Repositories\CommentsRepositoryInterface;
use src\Repositories\UserRepository;

class CreateComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentRepository,
        private TokenAuthInterface $auth
    ) { }
    public function handle(Request $request): Response
    {
        try {
            $data = $request->body(['post_uuid', 'text']);
            try {
                $u = $this->auth->user($request);
            } catch (AuthException $ex) {
                return new ErrorResponse($ex->getMessage());
            }
            $aUuid = $u->getUuid();
            $pUuid = new UUID($data['post_uuid']);
            $t = $data['text'];
            if (empty($text)) {
                throw new \InvalidArgumentException('Text cannot be empty');
            }
            $comment = new Comment(UUID::random(), $aUuid, $pUuid, $t);
            $this->commentRepository->save($comment);
            return new SuccessfullResponse(['message' => 'Comment created successfully']);
        } catch (\Exception $ex) {
            return new ErrorResponse($ex->getMessage());
        }
    }
}